import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Bar } from 'react-chartjs-2';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

export default function Dashboard() {
    // Sample data for charts
    const subscribedData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'Subscribed Students',
                data: [120, 150, 170, 200, 250, 300],
                backgroundColor: '#4CAF50',
            },
        ],
    };

    const unsubscribedData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'Unsubscribed Students',
                data: [20, 30, 15, 10, 8, 5],
                backgroundColor: '#FF5722',
            },
        ],
    };

    const allStudentsData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'All Students',
                data: [140, 180, 185, 210, 258, 305],
                backgroundColor: '#2196F3',
            },
        ],
    };

    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />
            
            <div className="py-6 bg-gray-100">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Dashboard Header */}
                    <div className="text-center mb-8">
                        <h1 className="text-3xl font-semibold text-gray-800">Welcome to your Dashboard</h1>
                        <p className="mt-2 text-lg text-gray-600">Monitor student activity and insights.</p>
                    </div>

                    {/* Dashboard Sections */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {/* All Students */}
                        <div className="bg-white shadow-md rounded-lg p-6">
                            <h2 className="text-2xl font-semibold text-gray-800 text-center">
                                All Students
                            </h2>
                            <p className="mt-2 text-lg text-gray-600 text-center">
                                Total: 305 Students
                            </p>
                            <Bar data={allStudentsData} />
                        </div>
                        {/* Subscribed Students */}
                        <div className="bg-white shadow-md rounded-lg p-6">
                            <h2 className="text-2xl font-semibold text-gray-800 text-center">
                                Subscribed Students
                            </h2>
                            <p className="mt-2 text-lg text-gray-600 text-center">
                                Total: 300 Students
                            </p>
                            <Bar data={subscribedData} />
                        </div>

                        {/* Unsubscribed Students */}
                        <div className="bg-white shadow-md rounded-lg p-6">
                            <h2 className="text-2xl font-semibold text-gray-800 text-center">
                                Unsubscribed Students
                            </h2>
                            <p className="mt-2 text-lg text-gray-600 text-center">
                                Total: 5 Students
                            </p>
                            <Bar data={unsubscribedData} />
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
