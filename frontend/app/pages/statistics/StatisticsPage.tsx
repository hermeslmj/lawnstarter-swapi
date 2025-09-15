import React, { useEffect, useState } from 'react';
import { Link } from 'react-router';
import { httpRequest } from '~/helpers/HttpHelper';
import type { Statistics } from '~/types/types';

const StatisticsPage: React.FC = () => {
  const [stats, setStats] = useState<Statistics[] | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [slowQueries, setSlowQueries] = useState<any[]>([]);
  const [mostFrequentRequests, setMostFrequentRequests] = useState<any[]>([]);
  const [averageExecutionTime, setAverageExecutionTime] = useState<string | undefined>(undefined);

  useEffect(() => {
    const fetchStatistics = async () => {
      try {
        const data = await httpRequest<Statistics[]>('http://localhost/api/statistics');

        var slowQueries = JSON.parse(data?.find(x => x.description === 'slowQueries')?.value as unknown as string || '[]');
        var mostFrequentRequests = JSON.parse(data?.find(x => x.description === 'mostFrequentRequests')?.value as unknown as string || '[]');
        var averageExecutionTimeRaw = data?.find(x => x.description === 'averageExecutionTime')?.value;
        const averageExecutionTime = averageExecutionTimeRaw !== undefined && averageExecutionTimeRaw !== null
          ? Number(averageExecutionTimeRaw)
          : null;
        setSlowQueries(slowQueries);
        setMostFrequentRequests(mostFrequentRequests);
        setAverageExecutionTime(averageExecutionTime?.toFixed(2));
        
        setStats(data);
      } catch (err: any) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchStatistics();
  }, []);

  if (loading) {
    return (
       <div className="flex items-center justify-center min-h-screen m-auto min-h-[40vh] ">
          <div className="w-10 h-10 border-4 border-green-500 border-t-transparent rounded-full animate-spin"></div>
        </div>
    );
  }

  if (error) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-lg text-red-600">Error: {error}</div>
      </div>
    );
  }

  return (
    <div className="w-full min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        <h1 className="text-3xl font-bold text-gray-800 mb-8">System Statistics</h1>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="bg-white rounded-lg shadow-sm p-6">
            <h2 className="text-xl font-semibold text-gray-700 mb-4">Average Response Time(s)</h2>
            <div className="text-4xl font-bold text-blue-600">
              {averageExecutionTime}
            </div>
          </div>
          <div className="bg-white rounded-lg shadow-sm p-6 md:col-span-2">
            <h2 className="text-xl font-semibold text-gray-700 mb-4">Top 5 Slowest Requests</h2>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="text-left border-b border-gray-200">
                    <th className="pb-3">Query</th>
                    <th className="pb-3">Time (s)</th>
                    <th className="pb-3">Date</th>
                  </tr>
                </thead>
                <tbody>
                  {slowQueries.map((request, index) => (
                    <tr key={index} className="border-b border-gray-100">
                      <td className="py-2 text-sm">{request.query}</td>
                      <td className="py-2 text-sm font-medium text-red-600">
                        {request.execution_time}
                      </td>
                      <td className="py-2 text-sm text-gray-500">
                        {new Date(request.created_at).toLocaleDateString()}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
          <div className="bg-white rounded-lg shadow-sm p-6 md:col-span-3">
            <h2 className="text-xl font-semibold text-gray-700 mb-4">Most Frequent Requests</h2>
            <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
              {mostFrequentRequests.map((request, index) => (
                <div key={index} className="p-4 bg-gray-50 rounded-lg">
                  <div className="text-sm text-gray-600 mb-2">
                    {request.query}
                  </div>
                  <div className="text-2xl font-bold text-green-600">
                    {request.count}
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
        <div className="mt-8 flex">
          <Link
            to="/"
            className="inline-block bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700 transition-colors rounded-full"
          >
            BACK TO SEARCH
          </Link>
        </div>
      </div>
    </div>
  );
};

export default StatisticsPage;