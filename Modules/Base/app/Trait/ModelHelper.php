<?php

namespace Modules\Base\Trait;

trait ModelHelper {

    /**
     * return translation of the model if force is true it must return exactly the translation of the locale
     * if translation of the locale is not found it will return the first translation
     *
     * @param string|null $col
     * @param string|null $locale
     * @param bool $force
     * @return mixed
     */
    public function smartTrans(string|null $col = null, string|null $locale = null, bool $force = false) : mixed
    {
        $locale ??= app()->getLocale();

        if(! is_null($trans = $this->translations->where('locale', $locale)->first())) {
            if(! is_null($col)) {
                return $trans->{$col} ?? null;
            }

            return $trans;
        }

        if(! $force) {
            if(! is_null($trans = $this->translations->first())) {
                if(! is_null($col)) {
                    return $trans->{$col} ?? null;
                }

                return $trans;
            }
        }

        return null;
    }

    /**
     * Return Translation image url
     *
     * @param string $collection
     * @param string|null $locale
     * @param string $conversion
     * @param bool $force
     * @return mixed
     */
    public function transImageUrl(string $collection, string|null $locale = null, string $conversion = '', bool $force = false) : mixed
    {
        $locale ??= app()->getLocale();

        if(! is_null($trans = $this->translations->where('locale', $locale)->first())) {
            return $trans->getFirstMedia($collection)?->getUrl($conversion) ?? '';
        }

        if(! $force) {
            if(! is_null($trans = $this->translations->first())) {
                return $trans->getFirstMedia($collection)?->getUrl($conversion) ?? '';
            }
        }

        return null;
    }

    /**
     * Return status of the model
     *
     * @return bool
     */
    public function isActiveStatus() : bool
    {
        return empty($this->{$this->getDisabledAtColumn()});
    }
}
