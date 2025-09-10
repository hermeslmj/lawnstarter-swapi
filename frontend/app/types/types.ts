// src/types.ts

export type SearchType = 'People' | 'Movies';

export interface SearchResult {
  id: string; // Or number, depending on your API
  name: string;
  // Add other properties if your results will have more data
}