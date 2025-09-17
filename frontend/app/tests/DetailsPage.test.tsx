import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen, waitFor } from '@testing-library/react';
import { MemoryRouter, Route, Routes } from 'react-router';
import DetailsPage from '../pages/details/DetailsPage';

// Mock httpRequest helper
vi.mock('../helpers/HttpHelper', () => ({
  httpRequest: vi.fn(),
}));

import { httpRequest } from '../helpers/HttpHelper';

function renderWithRouter(route: string) {
  return render(
    <MemoryRouter initialEntries={[route]}>
      <Routes>
        <Route path="/details/:type/:id" element={<DetailsPage />} />
        <Route path="/" element={<div>Search Page</div>} />
      </Routes>
    </MemoryRouter>
  );
}

describe('DetailsPage', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('shows error message on fetch error', async () => {
    (httpRequest as any).mockRejectedValue(new Error('Network error'));
    renderWithRouter('/details/people/1');
    await waitFor(() => {
      expect(screen.getByText(/Error: Network error/i)).toBeDefined();
    });
  });

  it('shows not found message if no details', async () => {
    (httpRequest as any).mockResolvedValue({ content: null });
    renderWithRouter('/details/people/1');
    await waitFor(() => {
      expect(screen.getByText(/No details found/i)).toBeDefined();
    });
  });

  it('renders people details and related movies', async () => {
    (httpRequest as any).mockResolvedValue({
      content: {
        uid: '1',
        name: 'Luke Skywalker',
        birthYear: '19BBY',
        gender: 'male',
        eyecolor: 'blue',
        haircolor: 'blond',
        height: '172',
        mass: '77',
        movies: [
          { uid: '10', title: 'A New Hope' },
          { uid: '11', title: 'The Empire Strikes Back' },
        ],
      },
    });
    renderWithRouter('/details/people/1');
    await waitFor(() => {
      expect(screen.getByText('Luke Skywalker')).toBeDefined();
      expect(screen.getByText('Birth Year: 19BBY')).toBeDefined();
      expect(screen.getByText('Movies')).toBeDefined();
      expect(screen.getByText('A New Hope')).toBeDefined();
      expect(screen.getByText('The Empire Strikes Back')).toBeDefined();
    });
  });

  it('renders film details and related characters', async () => {
    (httpRequest as any).mockResolvedValue({
      content: {
        uid: '10',
        title: 'A New Hope',
        openingCrawl: 'It is a period of civil war...',
        characters: [
          { uid: '1', name: 'Luke Skywalker' },
          { uid: '2', name: 'Leia Organa' },
        ],
      },
    });
    renderWithRouter('/details/films/10');
    await waitFor(() => {
      expect(screen.getByText('A New Hope')).toBeDefined();
      expect(screen.getByText('Opening Crawl')).toBeDefined();
      expect(screen.getByText('It is a period of civil war...')).toBeDefined();
      expect(screen.getByText('Characters')).toBeDefined();
      expect(screen.getByText('Luke Skywalker')).toBeDefined();
      expect(screen.getByText('Leia Organa')).toBeDefined();
    });
  });

  it('has a back to search link', async () => {
    (httpRequest as any).mockResolvedValue({
      content: {
        uid: '1',
        name: 'Luke Skywalker',
        birthYear: '19BBY',
        gender: 'male',
        eyecolor: 'blue',
        haircolor: 'blond',
        height: '172',
        mass: '77',
        movies: [],
      },
    });
    renderWithRouter('/details/people/1');
    await waitFor(() => {
      const backLink = screen.getByRole('link', { name: /back to search/i });
      expect(backLink).toBeDefined();
      expect(backLink.getAttribute('href')).toBe('/');
    });
  });
});