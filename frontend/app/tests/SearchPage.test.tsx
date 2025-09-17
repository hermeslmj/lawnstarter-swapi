import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { MemoryRouter } from 'react-router';
import SearchPage from '../pages/SearchPage';

// Mock httpRequest helper
vi.mock('../helpers/HttpHelper', () => ({
  httpRequest: vi.fn(),
}));

import { httpRequest } from '../helpers/HttpHelper';

const renderWithRouter = (ui: React.ReactElement) =>
  render(<MemoryRouter>{ui}</MemoryRouter>);

describe('SearchPage', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('renders search form and results section', () => {
    renderWithRouter(<SearchPage />);
    expect(screen.getByRole('textbox')).toBeDefined();
    expect(screen.getByText(/Results/i)).toBeDefined();
  });

  it('shows loading state when searching', async () => {
    (httpRequest as any).mockImplementation(() => new Promise(() => {})); // never resolves
    renderWithRouter(<SearchPage />);
    fireEvent.change(screen.getByRole('textbox'), { target: { value: 'Luke' } });
    fireEvent.click(screen.getByRole('button', { name: /search/i }));
    expect(await screen.findAllByText(/Searching.../i)).toBeDefined();
  });

  it('shows error message on failed search', async () => {
    (httpRequest as any).mockRejectedValue(new Error('Network error'));
    renderWithRouter(<SearchPage />);
    fireEvent.change(screen.getByRole('textbox'), { target: { value: 'Vader' } });
    fireEvent.click(screen.getByRole('button', { name: /search/i }));
    expect(await screen.findByText(/Network error/i)).toBeDefined();
  });

  it('shows results after successful search', async () => {
    (httpRequest as any).mockResolvedValue({
      content: [
        { uid: '1', title: 'Luke Skywalker' },
        { uid: '2', title: 'Leia Organa' },
      ],
    });
    renderWithRouter(<SearchPage />);
    fireEvent.change(screen.getByRole('textbox'), { target: { value: 'Luke' } });
    fireEvent.click(screen.getByRole('button', { name: /search/i }));

    await waitFor(() => {
      expect(screen.getByText('Luke Skywalker')).toBeDefined();
      expect(screen.getByText('Leia Organa')).toBeDefined();
    });
  });

  it('shows empty state if no results', async () => {
    (httpRequest as any).mockResolvedValue({ content: [] });
    renderWithRouter(<SearchPage />);
    fireEvent.change(screen.getByRole('textbox'), { target: { value: 'Nobody' } });
    fireEvent.click(screen.getByRole('button', { name: /search/i }));

    await waitFor(() => {
      expect(screen.getByText(/zero matches/i)).toBeDefined();
    });
  });

  it('searches for films when the type is changed', async () => {
    (httpRequest as any).mockResolvedValue({
      content: [
        { uid: '10', title: 'A New Hope' },
        { uid: '11', title: 'The Empire Strikes Back' },
      ],
    });
    renderWithRouter(<SearchPage />);

    const filmsRadio = screen.getByLabelText(/movies/i);
    fireEvent.click(filmsRadio);


    fireEvent.change(screen.getByRole('textbox'), { target: { value: 'Star' } });
    fireEvent.click(screen.getByRole('button', { name: /search/i }));

    await waitFor(() => {
      expect(screen.getByText('A New Hope')).toBeDefined();
      expect(screen.getByText('The Empire Strikes Back')).toBeDefined();
    });
  });
});