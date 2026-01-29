import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';

interface Employee {
    id: number;
    name: string;
    employee_number: string;
}

interface Attendance {
    id: number;
    employee: Employee;
    date: string;
    check_in: string;
    check_out: string | null;
    status: string;
}

interface Props {
    attendances: { data: Attendance[] };
}

export default function AttendanceIndex({ attendances }: Props) {
    const handleClockIn = () => {
        router.post('/api/attendance', {}, {
            onSuccess: () => {
                // Refresh or showing toast
            }
        });
    };

    const handleClockOut = () => {
        router.put('/api/attendance/clock-out', {}, {
            onSuccess: () => {
                // Refresh
            }
        });
    };

    return (
        <AppLayout>
            <Head title="Attendance" />

            <div className="flex justify-between items-center mb-6">
                <h2 className="text-xl font-semibold text-gray-800">Attendance</h2>
                <div className="space-x-4">
                    <button
                        onClick={handleClockIn}
                        className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                    >
                        Clock In
                    </button>
                    <button
                        onClick={handleClockOut}
                        className="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600"
                    >
                        Clock Out
                    </button>
                </div>
            </div>

            <div className="bg-white shadow-sm rounded-lg overflow-hidden">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee
                            </th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Check In
                            </th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Check Out
                            </th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {attendances.data.map((record) => (
                            <tr key={record.id}>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {record.date}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {record.employee.name}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {record.check_in}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {record.check_out || '-'}
                                </td>
                                <td className="px-6 py-4 whitespace-nowrap">
                                    <span
                                        className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800`}
                                    >
                                        {record.status}
                                    </span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {attendances.data.length === 0 && (
                    <div className="p-6 text-center text-gray-500">
                        No attendance records found.
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
