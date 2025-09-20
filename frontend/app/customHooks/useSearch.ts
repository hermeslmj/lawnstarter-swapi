import { useState } from 'react';
import type { SearchType, SearchResult, HttpResponse } from '../types/types';
import { httpRequest } from '../helpers/HttpHelper';

const API_URL = 'http://localhost/api';

export function useSearch() {
  const [results, setResults] = useState<SearchResult[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);

  const search = async (type: SearchType, term: string) => {
    setLoading(true);
    setError(null);
    setResults([]);

    // Use the type as part of the URL path for dynamic entity support
    const url = `${API_URL}/${type}/?searchTerm=${encodeURIComponent(term)}`;

    try {
      const data = await httpRequest<HttpResponse>(url);
      const resultsArray: SearchResult[] = [];
      if (data && Array.isArray(data.content)) {
        data.content.forEach((item) => {
          resultsArray.push({ id: item.uid, name: item.title ?? item.name });
        });
      }
      setResults(resultsArray);
    } catch (err: any) {
      setError(err.message || 'Failed to fetch results. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return { results, loading, error, search };
}