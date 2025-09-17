import { describe, it, expect, vi } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import SearchForm from '../components/SearchForm/SearchForm';

describe('SearchForm', () => {
  it('renders with initial values', () => {
    render(
      <SearchForm
        initialSearchType="people"
        initialSearchTerm="Luke"
        onSearch={vi.fn()}
        loading={false}
      />
    );
    expect(screen.getByDisplayValue('Luke')).toBeDefined();
    expect(screen.getByLabelText('People')).toBeChecked();
    expect(screen.getByLabelText('Movies')).not.toBeChecked();
  });

  it('changes search type when radio is clicked', () => {
    render(
      <SearchForm
        initialSearchType="people"
        initialSearchTerm=""
        onSearch={vi.fn()}
        loading={false}
      />
    );
    const moviesRadio = screen.getByLabelText('Movies');
    fireEvent.click(moviesRadio);
    expect(moviesRadio).toBeChecked();
  });

  it('enables search button only when input is not empty', () => {
    render(
      <SearchForm
        initialSearchType="people"
        initialSearchTerm=""
        onSearch={vi.fn()}
        loading={false}
      />
    );
    const input = screen.getByPlaceholderText(/chewbacca/i);
    const button = screen.getByRole('button', { name: /search/i });
    expect(button).toBeDisabled();
    fireEvent.change(input, { target: { value: 'Leia' } });
    expect(button).not.toBeDisabled();
  });

  it('calls onSearch with correct values on submit', () => {
    const onSearch = vi.fn();
    render(
      <SearchForm
        initialSearchType="films"
        initialSearchTerm="Empire"
        onSearch={onSearch}
        loading={false}
      />
    );
    const button = screen.getByRole('button', { name: /search/i });
    fireEvent.click(button);
    expect(onSearch).toHaveBeenCalledWith('films', 'Empire');
  });

  it('shows loading text when loading is true', () => {
    render(
      <SearchForm
        initialSearchType="people"
        initialSearchTerm=""
        onSearch={vi.fn()}
        loading={true}
      />
    );
    expect(screen.getByRole('button')).toHaveTextContent(/searching/i);
  });
});