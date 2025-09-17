import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen, waitFor } from '@testing-library/react';
import { MemoryRouter } from 'react-router';
import StatisticsPage from '../pages/statistics/StatisticsPage';

// Mock httpRequest helper
vi.mock('~/helpers/HttpHelper', () => ({
  httpRequest: vi.fn(),
}));

import { httpRequest } from '~/helpers/HttpHelper';

const mockStats = [
  { description: 'averageExecutionTime', value: 1.23 },
  { description: 'slowQueries', value: JSON.stringify([
    { query: '/api/films', execution_time: 2.5, created_at: '2024-01-01T00:00:00Z' },
    { query: '/api/people', execution_time: 2.1, created_at: '2024-01-02T00:00:00Z' },
  ]) },
  { description: 'mostFrequentRequests', value: JSON.stringify([
    { query: '/api/films', count: 10 },
    { query: '/api/people', count: 8 },
  ]) },
];

const renderWithRouter = (ui: React.ReactElement) =>
  render(<MemoryRouter>{ui}</MemoryRouter>);

describe('StatisticsPage', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('shows error message on fetch error', async () => {
    (httpRequest as any).mockRejectedValue(new Error('Network error'));
    renderWithRouter(<StatisticsPage />);
    await waitFor(() => {
      expect(screen.getByText(/Error: Network error/i)).toBeDefined();
    });
  });

  it('renders statistics data', async () => {
    (httpRequest as any).mockResolvedValue(mockStats);
    renderWithRouter(<StatisticsPage />);
    await waitFor(() => {
      expect(screen.getByText('System Statistics')).toBeDefined();
      expect(screen.getByText('Average Response Time(s)')).toBeDefined();
      expect(screen.getByText('1.23')).toBeDefined();
      expect(screen.getByText('Top 5 Slowest Requests')).toBeDefined();
      expect(screen.getAllByText('/api/films')).toHaveLength(2);
      expect(screen.getAllByText('/api/people')).toHaveLength(2);
      expect(screen.getByText('Most Frequent Requests')).toBeDefined();
      expect(screen.getByText('10')).toBeDefined();
      expect(screen.getByText('8')).toBeDefined();
    });
  });

  it('renders back to search link', async () => {
    (httpRequest as any).mockResolvedValue(mockStats);
    renderWithRouter(<StatisticsPage />);
    await waitFor(() => {
      const backLink = screen.getByRole('link', { name: /back to search/i });
      expect(backLink).toBeDefined();
      expect(backLink.getAttribute('href')).toBe('/');
    });
  });
});