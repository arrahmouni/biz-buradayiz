@extends('front::layouts.master')

@section('content')
    <div class="bg-gray-50 min-h-screen py-8">
        <div class="container mx-auto px-5 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Provider Dashboard</h1>
                <p class="text-gray-500">Welcome back, {{ Auth::user()->name ?? 'Metro Towing' }}</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Calls</p>
                            <p class="text-2xl font-bold text-gray-800">342</p>
                        </div>
                        <i class="fas fa-phone-alt text-red-400 text-3xl"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Completed</p>
                            <p class="text-2xl font-bold text-gray-800">312</p>
                        </div>
                        <i class="fas fa-check-circle text-green-400 text-3xl"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Earnings</p>
                            <p class="text-2xl font-bold text-gray-800">$8,420</p>
                        </div>
                        <i class="fas fa-dollar-sign text-blue-400 text-3xl"></i>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Rating</p>
                            <p class="text-2xl font-bold text-gray-800">4.8 ★</p>
                        </div>
                        <i class="fas fa-star text-yellow-400 text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Left Column: Call Log + Activity -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Call Log Table -->
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">Recent Call Log</h2>
                            <a href="#" class="text-sm text-red-600 hover:underline">View all</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 text-sm text-gray-800">John Driver</td>
                                        <td class="px-6 py-3 text-sm text-gray-600">Towing</td>
                                        <td class="px-6 py-3 text-sm text-gray-500">2025-04-14 10:30</td>
                                        <td class="px-6 py-3"><span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Completed</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 text-sm text-gray-800">Sarah Miller</td>
                                        <td class="px-6 py-3 text-sm text-gray-600">Battery Jump</td>
                                        <td class="px-6 py-3 text-sm text-gray-500">2025-04-13 15:45</td>
                                        <td class="px-6 py-3"><span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">In Progress</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 text-sm text-gray-800">Mike Thompson</td>
                                        <td class="px-6 py-3 text-sm text-gray-600">Flat Tire</td>
                                        <td class="px-6 py-3 text-sm text-gray-500">2025-04-13 09:15</td>
                                        <td class="px-6 py-3"><span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Completed</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 text-sm text-gray-800">Emily Clark</td>
                                        <td class="px-6 py-3 text-sm text-gray-600">Lockout</td>
                                        <td class="px-6 py-3 text-sm text-gray-500">2025-04-12 20:00</td>
                                        <td class="px-6 py-3"><span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Cancelled</span></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 text-sm text-gray-800">Robert Lee</td>
                                        <td class="px-6 py-3 text-sm text-gray-600">Fuel Delivery</td>
                                        <td class="px-6 py-3 text-sm text-gray-500">2025-04-12 13:20</td>
                                        <td class="px-6 py-3"><span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Completed</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-3 border-t border-gray-100 text-sm text-gray-500">
                            Showing 5 of 342 calls
                        </div>
                    </div>

                    <!-- Upcoming schedule or additional info (optional) -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-3">Upcoming Scheduled Calls</h2>
                        <p class="text-gray-500 text-sm">No upcoming appointments. You're all caught up!</p>
                    </div>
                </div>

                <!-- Right Column: Subscription & Packages -->
                <div class="space-y-8">
                    <!-- Current Subscription Card -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2"><i class="fas fa-ticket-alt text-red-500"></i> Current Subscription</h2>
                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between items-center border-b pb-2">
                                <span class="text-gray-600">Package:</span>
                                <span class="font-semibold text-gray-800">Professional Towing</span>
                            </div>
                            <div class="flex justify-between items-center border-b pb-2">
                                <span class="text-gray-600">Status:</span>
                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-sm">Active</span>
                            </div>
                            <div class="flex justify-between items-center border-b pb-2">
                                <span class="text-gray-600">Expires on:</span>
                                <span class="font-semibold">May 15, 2025</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Auto-renewal:</span>
                                <span class="text-sm">Enabled</span>
                            </div>
                        </div>
                    </div>

                    <!-- Subscribe / Upgrade Packages -->
                    <div class="bg-white rounded-2xl shadow-sm p-6" x-data="{ selectedPackage: null, receiptFile: null, receiptPreview: null }">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2"><i class="fas fa-box-open text-red-500"></i> Upgrade or Change Package</h2>
                        <p class="text-gray-500 text-sm mt-1">Choose a new plan – payment via bank transfer only.</p>

                        <!-- Package Options (simplified) -->
                        <div class="mt-5 space-y-3">
                            <label class="flex items-center justify-between p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <div>
                                    <span class="font-semibold text-gray-800">Basic Towing</span>
                                    <span class="text-gray-500 text-sm ml-2">$49/month</span>
                                </div>
                                <input type="radio" name="package" value="basic" x-model="selectedPackage" class="text-red-500">
                            </label>
                            <label class="flex items-center justify-between p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <div>
                                    <span class="font-semibold text-gray-800">Professional Towing</span>
                                    <span class="text-gray-500 text-sm ml-2">$99/month</span>
                                </div>
                                <input type="radio" name="package" value="pro" x-model="selectedPackage" class="text-red-500">
                            </label>
                            <label class="flex items-center justify-between p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                                <div>
                                    <span class="font-semibold text-gray-800">Enterprise Towing</span>
                                    <span class="text-gray-500 text-sm ml-2">$199/month</span>
                                </div>
                                <input type="radio" name="package" value="enterprise" x-model="selectedPackage" class="text-red-500">
                            </label>
                        </div>

                        <!-- Bank Transfer Details (shown when a package is selected) -->
                        <div x-show="selectedPackage" x-transition class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h3 class="font-semibold text-gray-800">Bank Transfer Information</h3>
                            <div class="mt-2 space-y-1 text-sm text-gray-700">
                                <p><strong>Bank Name:</strong> Road Rescue Financial</p>
                                <p><strong>Account Name:</strong> Road Rescue Platform</p>
                                <p><strong>Account Number:</strong> 1234-5678-9012-3456</p>
                                <p><strong>IBAN:</strong> TR12 3456 7890 1234 5678 90</p>
                                <p><strong>Reference:</strong> Your Company Name + Package</p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Upload Payment Receipt (image/PDF)</label>
                                <input type="file" accept="image/*,application/pdf" @change="receiptFile = $event.target.files[0]; receiptPreview = URL.createObjectURL(receiptFile)" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                <div x-show="receiptPreview" class="mt-2">
                                    <p class="text-xs text-gray-500">Preview:</p>
                                    <img :src="receiptPreview" class="h-20 w-auto object-contain border rounded mt-1">
                                </div>
                            </div>
                            <div class="mt-4 flex gap-3">
                                <!-- WhatsApp send (simulated) -->
                                <a :href="'https://wa.me/1234567890?text=' + encodeURIComponent('New subscription request for package: ' + selectedPackage + '. Receipt attached. Please process.')" target="_blank" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                    <i class="fab fa-whatsapp"></i> Send via WhatsApp
                                </a>
                                <button type="button" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50">Cancel</button>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">After sending, admin will review and activate your new package within 24 hours.</p>
                        </div>
                    </div>

                    <!-- Support / Help Card -->
                    <div class="bg-red-50 rounded-2xl p-5 border border-red-100">
                        <i class="fas fa-headset text-red-500 text-2xl mb-2"></i>
                        <h3 class="font-bold text-gray-800">Need assistance?</h3>
                        <p class="text-gray-600 text-sm mt-1">Our support team is here to help.</p>
                        <a href="#" class="inline-block mt-3 text-red-600 text-sm font-semibold hover:underline">Contact Support →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Optional: additional JS for file preview, etc. (already handled by Alpine)
    </script>
@endpush
