// src/components/SearchResults.tsx
import React from 'react';
import type { SearchResult } from '../../types/types';
import './SearchResults.css'; // Assuming you'll create a SearchResults.css

interface SearchResultsProps {
  results: SearchResult[];
}

const SearchResults: React.FC<SearchResultsProps> = ({ results }) => {
  return (
    <div className="search-results-container">
      <h2>Results</h2>
      <div className="results-content">
        {results.length === 0 ? (
          <p className="no-matches">
            There are zero matches.
            <br />
            Use the form to search for People or Movies.
          </p>
        ) : (
          <ul>
            {results.map((item) => (
              <li key={item.id}>{item.name}</li>
            ))}
          </ul>
        )}
      </div>
    </div>
  );
};

export default SearchResults;