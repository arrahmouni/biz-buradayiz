<!DOCTYPE html>

<html lang="{{ $_LOCALE_ }}" dir="{{ $_DIR_ }}" class="admin-modern-login-root">

	<head>
		<!--begin::Meta-->
        @include('admin::includes.meta')
        @stack('meta')
		<!--end::Meta-->

		<!--begin::Fonts-->
        @include('admin::includes.font')
        @stack('font')
		<!--end::Fonts-->

		<!--begin::styles-->
        @include('admin::includes.style')
        @stack('style')
		<!--end::styles-->
	</head>
	<!--end::Head-->

	<!--begin::Body-->
	<body id="kt_body" class="admin-modern-login-page">

        @include('partials.page-loader')

		<div class="d-flex flex-column flex-root flex-root-modern">
			<div class="modern-login-wrapper">
				<div class="admin-login-background-section">
					<div class="admin-login-background-overlay"></div>
				</div>

				<div class="admin-login-form-section">
					<div class="admin-login-lang-switcher">
						<div class="menu menu-rounded menu-column menu-primary menu-state-bg fw-semibold w-100px" data-kt-menu="true">
							<div class="menu-item" data-kt-menu-trigger="hover" data-kt-menu-placement="right-start">

								@component('admin::components.other.hyperlink', [
										'options'       => [
											'menuLink'  => true,
										]
									])
									<span class="menu-title position-relative">
										{{ $_CUR_LOCALE_NAME_ }}
										@include('admin::components.other.image', [
											'options' => [
												'class' => 'w-25px h-25px rounded-1 ms-2',
												'src'   => config('admin.frontend.country_flag.current_local'),
												'alt'   => 'current local flag',
											]
										])
									</span>
								@endcomponent

								<div class="menu-sub menu-sub-dropdown p-3 w-200px">
									@include('admin::particles.languages.switcher')
								</div>
							</div>
						</div>
					</div>

					@yield('mainContent')
				</div>
			</div>
		</div>

		<!--begin::Javascript-->
        @include('admin::includes.script')
        @include('admin::helpers.script.index')
        @stack('script')
		<!--end::Page Custom Javascript-->
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>
