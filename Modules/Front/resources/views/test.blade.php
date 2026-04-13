@extends('front::layouts.master')

@section('content')
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 text-white py-16 md:py-24">
        <div class="container mx-auto px-5 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">
                Join Road Rescue <span class="text-red-400">Provider Network</span>
            </h1>
            <p class="text-gray-300 text-lg mt-4 max-w-2xl mx-auto">
                Grow your business, get more customers, and provide reliable roadside assistance with our platform.
            </p>
            <div class="w-20 h-1 bg-red-500 mx-auto mt-6 rounded-full"></div>
        </div>
    </div>

    <!-- Tabs & Packages Section -->
    <section class="bg-gray-50 py-16 md:py-20">
        <div class="container mx-auto px-5 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-red-600 font-semibold uppercase tracking-wide">Choose your service</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mt-2">Select a package that fits your business</h2>
                <p class="text-gray-600 mt-3">Each service has specific packages tailored to your needs. All packages are paid – no hidden fees.</p>
            </div>

            <!-- Tabs (service categories) -->
            <div x-data="{ activeTab: 'towing' }" class="max-w-6xl mx-auto">
                <!-- Tab Buttons -->
                <div class="flex flex-wrap justify-center gap-2 border-b border-gray-200 mb-8">
                    <button @click="activeTab = 'towing'" :class="{'border-red-500 text-red-600': activeTab === 'towing', 'border-transparent text-gray-600 hover:text-gray-800': activeTab !== 'towing'}" class="px-5 py-2.5 text-sm md:text-base font-semibold border-b-2 transition">
                        <i class="fas fa-truck mr-2"></i> Towing & Recovery
                    </button>
                    <button @click="activeTab = 'battery'" :class="{'border-red-500 text-red-600': activeTab === 'battery', 'border-transparent text-gray-600 hover:text-gray-800': activeTab !== 'battery'}" class="px-5 py-2.5 text-sm md:text-base font-semibold border-b-2 transition">
                        <i class="fas fa-car-battery mr-2"></i> Battery Services
                    </button>
                    <button @click="activeTab = 'tire'" :class="{'border-red-500 text-red-600': activeTab === 'tire', 'border-transparent text-gray-600 hover:text-gray-800': activeTab !== 'tire'}" class="px-5 py-2.5 text-sm md:text-base font-semibold border-b-2 transition">
                        <i class="fas fa-tire mr-2"></i> Tire Services
                    </button>
                    <button @click="activeTab = 'fuel'" :class="{'border-red-500 text-red-600': activeTab === 'fuel', 'border-transparent text-gray-600 hover:text-gray-800': activeTab !== 'fuel'}" class="px-5 py-2.5 text-sm md:text-base font-semibold border-b-2 transition">
                        <i class="fas fa-gas-pump mr-2"></i> Fuel Delivery
                    </button>
                    <button @click="activeTab = 'lockout'" :class="{'border-red-500 text-red-600': activeTab === 'lockout', 'border-transparent text-gray-600 hover:text-gray-800': activeTab !== 'lockout'}" class="px-5 py-2.5 text-sm md:text-base font-semibold border-b-2 transition">
                        <i class="fas fa-key mr-2"></i> Lockout Service
                    </button>
                </div>

                <!-- Tab Panels -->
                <div class="mt-6">
                    <!-- Towing Packages -->
                    <div x-show="activeTab === 'towing'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform" x-transition:enter-end="opacity-100 transform">
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Package Basic -->
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-800">Basic Towing</h3>
                                    <div class="mt-4 flex items-baseline">
                                        <span class="text-3xl font-extrabold text-gray-900">$49</span>
                                        <span class="text-gray-500 ml-1">/month</span>
                                    </div>
                                    <p class="text-gray-500 text-sm mt-2">Perfect for small tow operators</p>
                                    <ul class="mt-6 space-y-3">
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Up to 5 calls/month</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> 5% platform fee</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Basic dispatch support</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Listing in search results</li>
                                    </ul>
                                </div>
                                <div class="px-6 pb-6">
                                    <a href="#" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition">Choose Plan</a>
                                </div>
                            </div>
                            <!-- Package Professional -->
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-red-200 relative hover:shadow-xl transition">
                                <div class="absolute top-0 right-0 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">Popular</div>
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-800">Professional Towing</h3>
                                    <div class="mt-4 flex items-baseline">
                                        <span class="text-3xl font-extrabold text-gray-900">$99</span>
                                        <span class="text-gray-500 ml-1">/month</span>
                                    </div>
                                    <p class="text-gray-500 text-sm mt-2">For growing towing businesses</p>
                                    <ul class="mt-6 space-y-3">
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Up to 20 calls/month</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> 3% platform fee</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Priority dispatch</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Featured listing badge</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Monthly performance report</li>
                                    </ul>
                                </div>
                                <div class="px-6 pb-6">
                                    <a href="#" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition">Choose Plan</a>
                                </div>
                            </div>
                            <!-- Package Enterprise -->
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition">
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-800">Enterprise Towing</h3>
                                    <div class="mt-4 flex items-baseline">
                                        <span class="text-3xl font-extrabold text-gray-900">$199</span>
                                        <span class="text-gray-500 ml-1">/month</span>
                                    </div>
                                    <p class="text-gray-500 text-sm mt-2">For large fleets & heavy duty</p>
                                    <ul class="mt-6 space-y-3">
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Unlimited calls</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> 1% platform fee</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Dedicated account manager</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> API integration</li>
                                        <li class="flex items-center gap-2 text-gray-600"><i class="fas fa-check-circle text-green-500"></i> Custom branding on listings</li>
                                    </ul>
                                </div>
                                <div class="px-6 pb-6">
                                    <a href="#" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition">Contact Sales</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Battery Services Packages (similar structure) -->
                    <div x-show="activeTab === 'battery'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform" x-transition:enter-end="opacity-100 transform">
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="bg-white rounded-2xl shadow-lg p-6">
                                <h3 class="text-xl font-bold">Basic Battery</h3>
                                <div class="mt-4"><span class="text-3xl font-bold">$29</span><span class="text-gray-500">/month</span></div>
                                <ul class="mt-6 space-y-2 text-gray-600"><li>✔ Up to 10 jump-starts</li><li>✔ 5% fee</li><li>✔ Basic support</li></ul>
                                <a href="#" class="mt-6 block bg-red-600 text-white text-center py-2 rounded-lg">Choose</a>
                            </div>
                            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-red-200">
                                <h3 class="text-xl font-bold">Pro Battery</h3>
                                <div class="mt-4"><span class="text-3xl font-bold">$59</span><span class="text-gray-500">/month</span></div>
                                <ul class="mt-6 space-y-2 text-gray-600"><li>✔ Unlimited jump-starts</li><li>✔ 3% fee</li><li>✔ Priority dispatch</li><li>✔ Battery testing tools</li></ul>
                                <a href="#" class="mt-6 block bg-red-600 text-white text-center py-2 rounded-lg">Choose</a>
                            </div>
                            <div class="bg-white rounded-2xl shadow-lg p-6">
                                <h3 class="text-xl font-bold">Fleet Battery</h3>
                                <div class="mt-4"><span class="text-3xl font-bold">$99</span><span class="text-gray-500">/month</span></div>
                                <ul class="mt-6 space-y-2 text-gray-600"><li>✔ Multi-vehicle support</li><li>✔ 1% fee</li><li>✔ Dedicated manager</li></ul>
                                <a href="#" class="mt-6 block bg-red-600 text-white text-center py-2 rounded-lg">Contact</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tire Services Packages -->
                    <div x-show="activeTab === 'tire'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform" x-transition:enter-end="opacity-100 transform">
                        <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
                            <div class="bg-white rounded-2xl shadow-lg p-6">
                                <h3 class="text-xl font-bold">Standard Tire</h3>
                                <div class="mt-4"><span class="text-3xl font-bold">$39</span><span class="text-gray-500">/month</span></div>
                                <ul class="mt-6 space-y-2"><li>✔ Up to 15 tire changes</li><li>✔ 5% fee</li><li>✔ Basic roadside tools</li></ul>
                                <a href="#" class="mt-6 block bg-red-600 text-white text-center py-2 rounded-lg">Choose</a>
                            </div>
                            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-red-200">
                                <h3 class="text-xl font-bold">Premium Tire</h3>
                                <div class="mt-4"><span class="text-3xl font-bold">$79</span><span class="text-gray-500">/month</span></div>
                                <ul class="mt-6 space-y-2"><li>✔ Unlimited tire changes</li><li>✔ 3% fee</li><li>✔ Tire pressure system</li><li>✔ 24/7 support</li></ul>
                                <a href="#" class="mt-6 block bg-red-600 text-white text-center py-2 rounded-lg">Choose</a>
                            </div>
                        </div>
                    </div>

                    <!-- Fuel Delivery Packages -->
                    <div x-show="activeTab === 'fuel'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform" x-transition:enter-end="opacity-100 transform">
                        <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
                            <div class="bg-white rounded-2xl shadow-lg p-6">
                                <h3 class="text-xl font-bold">Basic Fuel</h3>
                                <div class="mt-4"><span class="text-3xl font-bold">$49</span><span class="text-gray-500">/month</span></div>
                                <ul class="mt-6 space-y-2"><li>✔ Up to 20 deliveries</li><li>✔ 5% fee</li><li>✔ Fuel tracking</li></ul>
                                <a href="#" class="mt-6 block bg-red-600 text-white text-center py-2 rounded-lg">Choose</a>
                            </div>
                            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-red-200">
                                <h3 class="text-xl font-bold">Fleet Fuel</h3>
                                <div class="mt-4"><span class="text-3xl font-bold">$149</span><span class="text-gray-500">/month</span></div>
                                <ul class="mt-6 space-y-2"><li>✔ Unlimited deliveries</li><li>✔ 2% fee</li><li>✔ Bulk fuel discounts</li></ul>
                                <a href="#" class="mt-6 block bg-red-600 text-white text-center py-2 rounded-lg">Contact</a>
                            </div>
                        </div>
                    </div>

                    <!-- Lockout Services Packages -->
                    <div x-show="activeTab === 'lockout'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform" x-transition:enter-end="opacity-100 transform">
                        <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
                            <div class="bg-white rounded-2xl shadow-lg p-6">
                                <h3 class="text-xl font-bold">Basic Lockout</h3>
                                <div class="mt-4"><span class="text-3xl font-bold">$29</span><span class="text-gray-500">/month</span></div>
                                <ul class="mt-6 space-y-2"><li>✔ Up to 10 lockouts</li><li>✔ 5% fee</li><li>✔ Standard tools</li></ul>
                                <a href="#" class="mt-6 block bg-red-600 text-white text-center py-2 rounded-lg">Choose</a>
                            </div>
                            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-red-200">
                                <h3 class="text-xl font-bold">Pro Lockout</h3>
                                <div class="mt-4"><span class="text-3xl font-bold">$69</span><span class="text-gray-500">/month</span></div>
                                <ul class="mt-6 space-y-2"><li>✔ Unlimited lockouts</li><li>✔ 3% fee</li><li>✔ Advanced key tools</li><li>✔ 24/7 priority</li></ul>
                                <a href="#" class="mt-6 block bg-red-600 text-white text-center py-2 rounded-lg">Choose</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration CTA Banner -->
    <div class="bg-red-600 text-white py-12">
        <div class="container mx-auto px-5 text-center">
            <i class="fas fa-truck text-4xl mb-3 opacity-90"></i>
            <h2 class="text-2xl md:text-3xl font-bold">Ready to become a Road Rescue provider?</h2>
            <p class="text-red-100 mt-2">Join hundreds of trusted service providers in our network.</p>
            <a href="#" class="mt-6 inline-block bg-white text-red-600 px-8 py-3 rounded-full font-bold shadow-lg hover:shadow-xl transition transform hover:scale-105">
                Register Your Company Now
            </a>
            <p class="text-red-100 text-sm mt-4">No setup fees. Cancel anytime.</p>
        </div>
    </div>

    <!-- FAQ / Benefits Section -->
    <section class="bg-white py-12">
        <div class="container mx-auto px-5 lg:px-8 max-w-4xl">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <i class="fas fa-chart-line text-red-500 text-2xl mb-3"></i>
                    <h3 class="text-xl font-bold text-gray-800">Grow your business</h3>
                    <p class="text-gray-600 mt-1">Access thousands of customers looking for reliable roadside assistance in your area.</p>
                </div>
                <div>
                    <i class="fas fa-credit-card text-red-500 text-2xl mb-3"></i>
                    <h3 class="text-xl font-bold text-gray-800">Transparent pricing</h3>
                    <p class="text-gray-600 mt-1">Pay only a small platform fee per completed job. No hidden costs.</p>
                </div>
                <div>
                    <i class="fas fa-clock text-red-500 text-2xl mb-3"></i>
                    <h3 class="text-xl font-bold text-gray-800">24/7 support</h3>
                    <p class="text-gray-600 mt-1">Our dispatch and support team is always available to assist you.</p>
                </div>
                <div>
                    <i class="fas fa-star text-red-500 text-2xl mb-3"></i>
                    <h3 class="text-xl font-bold text-gray-800">Build reputation</h3>
                    <p class="text-gray-600 mt-1">Collect reviews and ratings to become a top-rated provider.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Alpine.js for tabs (if not already in layout) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
