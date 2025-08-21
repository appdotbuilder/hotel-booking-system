import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Hotel Booking System">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            
            <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
                {/* Header */}
                <div className="bg-white dark:bg-gray-900 shadow-sm border-b">
                    <div className="container mx-auto px-4 py-4">
                        <nav className="flex items-center justify-between">
                            <Link href="/" className="text-2xl font-bold text-blue-600">
                                🏨 Luxury Hotel & Resort
                            </Link>
                            <div className="flex items-center gap-4">
                                {auth?.user ? (
                                    <>
                                        <span className="text-sm text-gray-600">Welcome, {auth.user.name}</span>
                                        <Link href="/bookings" className="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200">
                                            My Bookings
                                        </Link>
                                        <Link href="/logout" method="post" className="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                            Logout
                                        </Link>
                                    </>
                                ) : (
                                    <>
                                        <Link href="/login" className="px-4 py-2 border border-blue-600 text-blue-600 rounded-md hover:bg-blue-50">
                                            Login
                                        </Link>
                                        <Link href="/register" className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                            Register
                                        </Link>
                                    </>
                                )}
                            </div>
                        </nav>
                    </div>
                </div>

                {/* Hero Section */}
                <div className="bg-white dark:bg-gray-900">
                    <div className="container mx-auto px-4 py-16 text-center">
                        <h1 className="text-6xl font-bold text-gray-900 dark:text-white mb-6">
                            🏨 Welcome to Luxury Hotel & Resort
                        </h1>
                        <p className="text-xl text-gray-600 dark:text-gray-300 mb-12 max-w-3xl mx-auto">
                            Experience unparalleled comfort and elegance in our premium accommodations. 
                            Our comprehensive hotel booking system offers multiple user roles and streamlined reservation management.
                        </p>
                        
                        {/* Key Features */}
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto mb-12">
                            <div className="text-center p-6 bg-blue-50 rounded-lg">
                                <div className="text-4xl mb-4">🛏️</div>
                                <h3 className="text-xl font-semibold mb-3">Premium Rooms</h3>
                                <p className="text-gray-600">
                                    Choose from standard rooms to presidential suites with luxury amenities and modern facilities.
                                </p>
                            </div>
                            <div className="text-center p-6 bg-green-50 rounded-lg">
                                <div className="text-4xl mb-4">📅</div>
                                <h3 className="text-xl font-semibold mb-3">Easy Booking</h3>
                                <p className="text-gray-600">
                                    Simple and secure online reservation system with real-time availability checking.
                                </p>
                            </div>
                            <div className="text-center p-6 bg-purple-50 rounded-lg">
                                <div className="text-4xl mb-4">👥</div>
                                <h3 className="text-xl font-semibold mb-3">Multi-Role Access</h3>
                                <p className="text-gray-600">
                                    Superadmin, Admin, Staff, and Guest roles with appropriate permissions and features.
                                </p>
                            </div>
                        </div>

                        {/* System Features */}
                        <div className="bg-gray-50 rounded-lg p-8 max-w-5xl mx-auto">
                            <h2 className="text-3xl font-bold mb-6">🌟 System Features</h2>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                                <div>
                                    <h4 className="font-semibold text-lg mb-3">🔐 User Management</h4>
                                    <ul className="space-y-2 text-gray-600">
                                        <li>• Superadmin can manage all users and system settings</li>
                                        <li>• Admin can manage rooms and view all bookings</li>
                                        <li>• Staff can manage bookings and assist guests</li>
                                        <li>• Guests can view rooms and make reservations</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 className="font-semibold text-lg mb-3">🏨 Room Management</h4>
                                    <ul className="space-y-2 text-gray-600">
                                        <li>• Add, edit, and delete hotel rooms</li>
                                        <li>• Room types, capacity, and pricing management</li>
                                        <li>• Amenities and room status tracking</li>
                                        <li>• Real-time availability checking</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 className="font-semibold text-lg mb-3">📋 Booking System</h4>
                                    <ul className="space-y-2 text-gray-600">
                                        <li>• Create, view, and manage reservations</li>
                                        <li>• Guest information and contact details</li>
                                        <li>• Check-in/check-out date management</li>
                                        <li>• Booking status tracking and cancellations</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 className="font-semibold text-lg mb-3">📊 Analytics & Reports</h4>
                                    <ul className="space-y-2 text-gray-600">
                                        <li>• Dashboard with key statistics</li>
                                        <li>• Daily check-ins and check-outs</li>
                                        <li>• Room occupancy and availability</li>
                                        <li>• Booking history and guest management</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Action Section */}
                <div className="container mx-auto px-4 py-16">
                    {auth?.user ? (
                        <div className="text-center bg-gradient-to-r from-green-600 to-blue-600 text-white rounded-lg p-12">
                            <h2 className="text-4xl font-bold mb-4">🎉 Welcome Back, {auth.user.name}!</h2>
                            <p className="text-xl mb-8 opacity-90">
                                Access your personalized dashboard and manage your hotel experience.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/bookings" className="bg-white text-green-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition">
                                    📋 View My Bookings
                                </Link>
                                <Link href="/rooms" className="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-green-600 transition">
                                    🏨 Browse Rooms
                                </Link>
                            </div>
                        </div>
                    ) : (
                        <div className="text-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg p-12">
                            <h2 className="text-4xl font-bold mb-4">🎯 Ready to Experience Luxury?</h2>
                            <p className="text-xl mb-8 opacity-90">
                                Join thousands of satisfied guests and discover our comprehensive hotel booking system.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link href="/register" className="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition">
                                    📝 Create Account
                                </Link>
                                <Link href="/login" className="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-blue-600 transition">
                                    🔑 Sign In
                                </Link>
                            </div>
                        </div>
                    )}

                    {/* Demo User Accounts */}
                    <div className="mt-16 bg-yellow-50 border border-yellow-200 rounded-lg p-8 max-w-4xl mx-auto">
                        <h3 className="text-2xl font-bold text-center mb-6">🔑 Demo User Accounts</h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div className="bg-white p-4 rounded-lg border">
                                <h4 className="font-semibold text-red-600 mb-2">👑 Superadmin</h4>
                                <p className="text-sm text-gray-600 mb-2">Full system access</p>
                                <p className="text-xs font-mono bg-gray-100 p-2 rounded">
                                    superadmin@hotel.com<br />
                                    password
                                </p>
                            </div>
                            <div className="bg-white p-4 rounded-lg border">
                                <h4 className="font-semibold text-blue-600 mb-2">🔧 Admin</h4>
                                <p className="text-sm text-gray-600 mb-2">Rooms & bookings</p>
                                <p className="text-xs font-mono bg-gray-100 p-2 rounded">
                                    admin@hotel.com<br />
                                    password
                                </p>
                            </div>
                            <div className="bg-white p-4 rounded-lg border">
                                <h4 className="font-semibold text-green-600 mb-2">👨‍💼 Staff</h4>
                                <p className="text-sm text-gray-600 mb-2">Booking management</p>
                                <p className="text-xs font-mono bg-gray-100 p-2 rounded">
                                    staff@hotel.com<br />
                                    password
                                </p>
                            </div>
                            <div className="bg-white p-4 rounded-lg border">
                                <h4 className="font-semibold text-purple-600 mb-2">🏠 Guest</h4>
                                <p className="text-sm text-gray-600 mb-2">Make bookings</p>
                                <p className="text-xs font-mono bg-gray-100 p-2 rounded">
                                    guest@hotel.com<br />
                                    password
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <footer className="bg-gray-900 text-white py-12">
                    <div className="container mx-auto px-4 text-center">
                        <div className="mb-6">
                            <h3 className="text-2xl font-bold mb-2">🏨 Luxury Hotel & Resort</h3>
                            <p className="text-gray-400">Comprehensive Hotel Room Booking System</p>
                        </div>
                        
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 text-left md:text-center">
                            <div>
                                <h4 className="font-semibold mb-3">🌟 Features</h4>
                                <ul className="text-gray-400 space-y-2">
                                    <li>Multi-role user management</li>
                                    <li>Room availability tracking</li>
                                    <li>Booking management system</li>
                                    <li>Real-time dashboard analytics</li>
                                </ul>
                            </div>
                            <div>
                                <h4 className="font-semibold mb-3">👥 User Roles</h4>
                                <ul className="text-gray-400 space-y-2">
                                    <li>Superadmin - Full access</li>
                                    <li>Admin - Room & booking management</li>
                                    <li>Staff - Booking assistance</li>
                                    <li>Guest - Room reservations</li>
                                </ul>
                            </div>
                            <div>
                                <h4 className="font-semibold mb-3">🔗 Quick Links</h4>
                                <div className="space-y-2">
                                    <Link href="/rooms" className="block text-gray-400 hover:text-white">
                                        Browse Rooms
                                    </Link>
                                    <Link href="/bookings" className="block text-gray-400 hover:text-white">
                                        Manage Bookings
                                    </Link>
                                    <Link href="/login" className="block text-gray-400 hover:text-white">
                                        User Login
                                    </Link>
                                    <Link href="/register" className="block text-gray-400 hover:text-white">
                                        Create Account
                                    </Link>
                                </div>
                            </div>
                        </div>
                        
                        <div className="border-t border-gray-700 pt-6">
                            <p className="text-gray-400">
                                © 2024 Luxury Hotel & Resort Booking System. Built with Laravel, React, and TypeScript.
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}