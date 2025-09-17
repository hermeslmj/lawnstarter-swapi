import { describe, it, expect, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import SearchResultsRow from '../components/SearchResultsRows/SearchResultsRows';

describe('SearchResultsRow', () => {
  it('renders the name and button', () => {
    render(<SearchResultsRow name="Luke Skywalker" onViewDetails={() => {}} />);
    expect(screen.getByText('Luke Skywalker')).toBeDefined();
    expect(screen.getByRole('button', { name: /see details/i })).toBeDefined();
  });

  it('calls onViewDetails when button is clicked', () => {
    const onViewDetails = vi.fn();
    render(<SearchResultsRow name="Leia Organa" onViewDetails={onViewDetails} />);
    const button = screen.getByRole('button', { name: /see details/i });
    fireEvent.click(button);
    expect(onViewDetails).toHaveBeenCalledTimes(1);
  });
});