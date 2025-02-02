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

export default function Dashboard({ users }) {
    // Helper to get month names
    const monthLabels = [
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
    ];

    // Initialize monthly counts
    const monthlySubscribedCounts = new Array(12).fill(0);
    const monthlyUnsubscribedCounts = new Array(12).fill(0);
    const monthlyAllCounts = new Array(12).fill(0);

   users.forEach(user => {
        const createdAt = new Date(user.created_at); 
        const monthIndex = createdAt.getMonth(); 

        monthlyAllCounts[monthIndex]++;

        if (user.isSubscribed) {
            monthlySubscribedCounts[monthIndex]++;
        } else {
            monthlyUnsubscribedCounts[monthIndex]++;
        }
    });

    const chartData = (label, data, color) => ({
        labels: monthLabels,
        datasets: [
            {
                label,
                data,
                backgroundColor: color,
            },
        ],
    });

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
                            
                            <Bar data={chartData('All Students', monthlyAllCounts, '#2196F3')} />
                        </div>

                        {/* Subscribed Students */}
                        <div className="bg-white shadow-md rounded-lg p-6">
                            <h2 className="text-2xl font-semibold text-gray-800 text-center">
                                Subscribed Students
                            </h2>
                            <Bar data={chartData('Subscribed Students', monthlySubscribedCounts, '#4CAF50')} />
                        </div>

                        {/* Unsubscribed Students */}
                        <div className="bg-white shadow-md rounded-lg p-6">
                            <h2 className="text-2xl font-semibold text-gray-800 text-center">
                                Unsubscribed Students
                            </h2>
                            <Bar data={chartData('Unsubscribed Students', monthlyUnsubscribedCounts, '#FF5722')} />
                        </div>
                    </div>
                </div>
                <div className="col-12 mt-4 shadow p-2">
                    <h1 className='h3 text-center'>All Users</h1>
                    <table className='table'>
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            {users.map((user, index)=>(
                                <tr>
                                    <td>{user.first_name}</td>
                                    <td>{user.last_name}</td>
                                    <td>{user.email}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
