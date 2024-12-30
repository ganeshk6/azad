import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard() {
    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />
            
            <div className="py-6 bg-gray-100">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Dashboard Header */}
                    <div className="text-center mb-8">
                        <h1 className="text-3xl font-semibold text-gray-800">Welcome to your Dashboard</h1>
                        <p className="mt-2 text-lg text-gray-600">Here’s an overview of your activities and recent updates.</p>
                    </div>

                    {/* Dashboard Cards */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {/* Card 1 */}
                        <div className="bg-white shadow-md rounded-lg p-6 text-center">
                            <h2 className="text-2xl font-semibold text-gray-800">Total Sales</h2>
                            <p className="mt-2 text-lg text-gray-600">$12,450</p>
                        </div>

                        {/* Card 2 */}
                        <div className="bg-white shadow-md rounded-lg p-6 text-center">
                            <h2 className="text-2xl font-semibold text-gray-800">Pending Orders</h2>
                            <p className="mt-2 text-lg text-gray-600">18 Orders</p>
                        </div>

                        {/* Card 3 */}
                        <div className="bg-white shadow-md rounded-lg p-6 text-center">
                            <h2 className="text-2xl font-semibold text-gray-800">Customer Feedback</h2>
                            <p className="mt-2 text-lg text-gray-600">4.5/5</p>
                        </div>
                    </div>

                    {/* Recent Activities */}
                    <div className="mt-8 bg-white shadow-md rounded-lg p-6">
                        <h2 className="text-2xl font-semibold text-gray-800">Recent Activities</h2>
                        <ul className="mt-4 space-y-4">
                            <li className="flex items-center space-x-3">
                                <span className="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                <p className="text-lg text-gray-600">Completed 3 orders successfully</p>
                            </li>
                            <li className="flex items-center space-x-3">
                                <span className="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
                                <p className="text-lg text-gray-600">2 orders awaiting approval</p>
                            </li>
                            <li className="flex items-center space-x-3">
                                <span className="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                <p className="text-lg text-gray-600">1 order has been canceled</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
