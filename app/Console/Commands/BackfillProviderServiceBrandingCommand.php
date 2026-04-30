<?php

namespace App\Console\Commands;

use App\Support\ProviderServiceBanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;

class BackfillProviderServiceBrandingCommand extends Command
{
    protected $signature = 'providers:backfill-service-branding
                            {--dry-run : Show what would change without saving}
                            {--company : Only update company names}
                            {--images : Only attach service banner and user avatar images}
                            {--force-company : Overwrite non-empty company names}
                            {--force-images : Replace existing service banner and user avatar media}';
                            

    protected $description = 'Fill missing service provider company names, provider_service_image media (public/images/services), and user_image avatars (modules/admin/metronic/demo/media/avatars).';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $companyOnly = (bool) $this->option('company');
        $imagesOnly = (bool) $this->option('images');
        $doCompany = ! $imagesOnly || $companyOnly;
        $doImages = ! $companyOnly || $imagesOnly;
        $forceCompany = (bool) $this->option('force-company');
        $forceImages = (bool) $this->option('force-images');

        if ($companyOnly && $imagesOnly) {
            $this->error('Use either --company, --images, or neither (both operations).');

            return self::INVALID;
        }

        $providers = User::query()
            ->where('type', UserType::ServiceProvider)
            ->with(['service.translations']);

        $savedCompanies = 0;
        $savedImages = 0;
        /** @var list<string> $errors */
        $errors = [];
        $demoAvatarPaths = $doImages ? $this->demoAvatarPaths() : [];

        foreach ($providers->lazyById() as $user) {

            $user->loadMissing('service.translations');

            if ($doCompany && ($forceCompany || blank($user->company_name))) {
                $suggested = $this->thematicCompanyName($user);

                $previous = blank($user->company_name) ? '(empty)' : $user->company_name;
                $this->line(sprintf('[company] #%d: %s → %s', $user->id, $previous, $suggested));

                if (! $dryRun) {
                    try {
                        DB::transaction(static function () use ($user, $suggested): void {
                            User::whereKey($user->id)->update(['company_name' => $suggested]);
                        });
                        $user->company_name = $suggested;
                        $savedCompanies++;
                    } catch (\Throwable $e) {
                        $errors[] = "User #{$user->id} company update: ".$e->getMessage();
                    }
                } else {
                    $savedCompanies++;
                }
            }

            if (! $doImages) {
                continue;
            }

            $user->loadMissing('service.translations');

            $hasServiceImage = $user->getMedia(User::SERVICE_IMAGE_MEDIA_COLLECTION)->isNotEmpty();
            if ($hasServiceImage && ! $forceImages) {
                if ($this->output->isVerbose()) {
                    $this->line("[image] #{$user->id}: skipped (service image already set)");
                }
            } else {
                try {
                    $sourcePath = ProviderServiceBanner::absolutePathForService($user->service);
                } catch (\RuntimeException $e) {
                    $errors[] = "User #{$user->id}: ".$e->getMessage();
                    $sourcePath = null;
                }

                if (isset($sourcePath)) {
                    $this->line(sprintf(
                        '[image] #%d %s%s → %s',
                        $user->id,
                        $hasServiceImage ? 'replace' : 'add',
                        $forceImages && $hasServiceImage ? ' (forced)' : '',
                        $sourcePath,
                    ));

                    if (! $dryRun) {
                        try {
                            DB::transaction(function () use ($user, $sourcePath): void {
                                $user->refresh();
                                $user->loadMissing('service.translations');
                                if ($user->getMedia(User::SERVICE_IMAGE_MEDIA_COLLECTION)->isNotEmpty()) {
                                    $user->clearMediaCollection(User::SERVICE_IMAGE_MEDIA_COLLECTION);
                                }
                                $user->addMedia($sourcePath)
                                    ->preservingOriginal()
                                    ->toMediaCollection(User::SERVICE_IMAGE_MEDIA_COLLECTION);
                            });
                            $savedImages++;
                        } catch (\Throwable $e) {
                            $errors[] = "User #{$user->id} image: ".$e->getMessage();
                        }
                    } else {
                        $savedImages++;
                    }
                }
            }

            $hasUserImage = $user->getMedia(User::MEDIA_COLLECTION)->isNotEmpty();
            if ($hasUserImage && ! $forceImages) {
                if ($this->output->isVerbose()) {
                    $this->line("[avatar] #{$user->id}: skipped (user image already set)");
                }
            } elseif ($demoAvatarPaths === []) {
                if ($this->output->isVerbose()) {
                    $this->line("[avatar] #{$user->id}: skipped (no demo avatars in metronic folder)");
                }
            } else {
                $avatarPath = fake()->randomElement($demoAvatarPaths);
                $this->line(sprintf(
                    '[avatar] #%d %s%s → %s',
                    $user->id,
                    $hasUserImage ? 'replace' : 'add',
                    $forceImages && $hasUserImage ? ' (forced)' : '',
                    $avatarPath,
                ));

                if (! $dryRun) {
                    try {
                        DB::transaction(function () use ($user, $avatarPath): void {
                            $user->refresh();
                            if ($user->getMedia(User::MEDIA_COLLECTION)->isNotEmpty()) {
                                $user->clearMediaCollection(User::MEDIA_COLLECTION);
                            }
                            $user->addMedia($avatarPath)
                                ->preservingOriginal()
                                ->toMediaCollection(User::MEDIA_COLLECTION);
                        });
                        $savedImages++;
                    } catch (\Throwable $e) {
                        $errors[] = "User #{$user->id} avatar: ".$e->getMessage();
                    }
                } else {
                    $savedImages++;
                }
            }
        }

        if ($dryRun) {
            $this->warn('Dry run: no database or media writes were performed.');
        }

        $label = $dryRun ? 'Rows that would be updated' : 'Rows updated';
        $this->table(
            [$label.' (company)', $label.' (service + avatar images)'],
            [[(string) $savedCompanies, (string) $savedImages]],
        );

        foreach ($errors as $message) {
            $this->error($message);
        }

        return $errors !== [] ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function demoAvatarPaths(): array
    {
        $avatarDir = public_path('modules/admin/metronic/demo/media/avatars');

        return glob($avatarDir.'/*.jpg') ?: [];
    }

    /**
     * Branded demo company label derived from English service title when possible.
     */
    private function thematicCompanyName(User $user): string
    {
        $user->loadMissing('service.translations');
        $service = $user->service;

        if ($service === null) {
            return fake()->company();
        }

        $label = trim((string) ($service->translate('en')?->name
            ?? $service->translate('tr')?->name
            ?? $service->smartTrans('name')));

        if ($label !== '') {
            $suffix = fake()->randomElement([' Ltd.', ' A.Ş.', ' Servis', ' Group', ' Hizmetleri']);

            return $label.$suffix;
        }

        return fake()->company();
    }
}
