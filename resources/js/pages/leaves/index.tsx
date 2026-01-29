import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';

interface LeaveType {
    id: number;
    name: string;
    days_allowed: number;
}

interface Leave {
    id: number;
    leave_type: LeaveType;
    start_date: string;
    end_date: string;
    reason: string;
    status: string;
}

interface Props {
    leaves: { data: Leave[] };
    leaveTypes: LeaveType[]; // Passed from controller/route
}

export default function LeaveIndex({ leaves, leaveTypes }: Props) {
    const [isApplying, setIsApplying] = useState(false);
    const [formData, setFormData] = useState({
        leave_type_id: '',
        start_date: '',
        end_date: '',
        reason: '',
    });

    const submitApplication = (e: React.FormEvent) => {
        e.preventDefault();
        router.post('/api/leaves', formData, {
            onSuccess: () => {
                setIsApplying(false);
                setFormData({ leave_type_id: '', start_date: '', end_date: '', reason: '' });
                // Refresh logic usually handled by Inertia reload
            }
        });
    };

    return (
        <AppLayout>
            <Head title="Leaves" />

            <div className="flex justify-between items-center mb-6">
                <h2 className="text-xl font-semibold text-gray-800">Leave Management</h2>
                <button
                    onClick={() => setIsApplying(!isApplying)}
                    className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    {isApplying ? 'Cancel Application' : 'Apply for Leave'}
                </button>
            </div>

            {isApplying && (
                <div className="bg-white p-6 rounded-lg shadow-md mb-6 border border-gray-100">
                    <h3 className="text-lg font-medium mb-4">New Leave Application</h3>
                    <form onSubmit={submitApplication} className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Leave Type</label>
                                <select
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value={formData.leave_type_id}
                                    onChange={(e) => setFormData({ ...formData, leave_type_id: e.target.value })}
                                    required
                                >
                                    <option value="">Select Type</option>
                                    {leaveTypes.map(type => (
                                        <option key={type.id} value={type.id}>{type.name} ({type.days_allowed} days)</option>
                                    ))}
                                </select>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input
                                        type="date"
                                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        value={formData.start_date}
                                        onChange={(e) => setFormData({ ...formData, start_date: e.target.value })}
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">End Date</label>
                                    <input
                                        type="date"
                                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        value={formData.end_date}
                                        onChange={(e) => setFormData({ ...formData, end_date: e.target.value })}
                                        required
                                    />
                                </div>
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Reason</label>
                            <textarea
                                className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                rows={3}
                                value={formData.reason}
                                onChange={(e) => setFormData({ ...formData, reason: e.target.value })}
                                required
                            />
                        </div>
                        <div className="flex justify-end">
                            <button
                                type="submit"
                                className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                            >
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            )}

            <div className="bg-white shadow-sm rounded-lg overflow-hidden">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {leaves.data.map((leave) => (
                            <tr key={leave.id}>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{leave.leave_type.name}</td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{leave.start_date}</td>
                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{leave.end_date}</td>
                                <td className="px-6 py-4 whitespace-nowrap">
                                    <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        ${leave.status === 'approved' ? 'bg-green-100 text-green-800' :
                                            leave.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                leave.status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'}`}>
                                        {leave.status}
                                    </span>
                                </td>
                                <td className="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{leave.reason}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {leaves.data.length === 0 && (
                    <div className="p-6 text-center text-gray-500">
                        No leave records found.
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
