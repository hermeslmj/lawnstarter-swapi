import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import Header from '../components/Header/Header';

describe('Header', () => {
  it('renders the header element', () => {
    render(<Header />);
    const header = screen.getByRole('banner');
    expect(header).toBeDefined();
  });

  it('renders the correct title', () => {
    render(<Header />);
    const title = screen.getByText('SWStarter');
    expect(title).toBeDefined();
    expect(title.className).toContain('text-2xl');
    expect(title.className).toContain('md:text-3xl');
    expect(title.className).toContain('font-bold');
    expect(title).toHaveClass('text-center');
  });
});