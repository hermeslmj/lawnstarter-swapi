import { describe, it, expect, vi } from 'vitest';
import { render, screen } from '@testing-library/react';
import { MemoryRouter } from 'react-router';
import SearchResults from '../components/SearchResults/SearchResults';

const renderWithRouter = (ui: React.ReactElement) =>
  render(<MemoryRouter>{ui}</MemoryRouter>);

describe('SearchResults', () => {
  it('shows empty message when results is empty', () => {
    renderWithRouter(<SearchResults results={[]} type="people" />);
    expect(screen.getByText(/there are zero matches/i)).toBeDefined();
    expect(screen.getByText(/use the form to search/i)).toBeDefined();
  });

  it('renders SearchResultsRow for each result', () => {
    const results = [
      { id: '1', name: 'Luke Skywalker' },
      { id: '2', name: 'Leia Organa' },
    ];
    renderWithRouter(<SearchResults results={results} type="people" />);
    expect(screen.getByText('Luke Skywalker')).toBeDefined();
    expect(screen.getByText('Leia Organa')).toBeDefined();
    expect(screen.getAllByRole('button', { name: /see details/i })).toHaveLength(2);
  });
});