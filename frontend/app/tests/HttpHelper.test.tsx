import { describe, it, expect, vi, beforeEach } from 'vitest';
import { httpRequest } from '../helpers/HttpHelper';

describe('httpRequest', () => {
  beforeEach(() => {
    vi.restoreAllMocks();
  });

  it('returns JSON data on success', async () => {
    const mockData = { foo: 'bar' };
    vi.stubGlobal('fetch', vi.fn(() =>
      Promise.resolve({
        ok: true,
        json: () => Promise.resolve(mockData),
      } as any)
    ));

    const result = await httpRequest<typeof mockData>('/api/test');
    expect(result).toEqual(mockData);
  });

  it('throws error on HTTP error', async () => {
    vi.stubGlobal('fetch', vi.fn(() =>
      Promise.resolve({
        ok: false,
        status: 404,
        text: () => Promise.resolve('Not Found'),
      } as any)
    ));

    await expect(httpRequest('/api/404')).rejects.toThrow(
      /HTTP error! status: 404, message: Not Found/
    );
  });

});