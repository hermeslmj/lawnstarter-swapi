// src/components/SearchResults.tsx
import React from 'react';
import type { SearchResult } from '../../types/types';
import SearchResultsRow from '../SearchResultsRows/SearchResultsRows';
import './SearchResults.css'; // Assuming you'll create a SearchResults.css

interface SearchResultsProps {
  results: SearchResult[];
  type: 'people' | 'films';
}

const SearchResults: React.FC<SearchResultsProps> = ({ results, type }) => {

  return (
    <div className="search-results-container">
      <div className='results-header'>
        <h1>Results</h1>
      </div>
      <div className="results-content">
        {results.length === 0 ? (
          <p className="no-matches">
            There are zero matches.
            <br />
            Use the form to search for People or Movies.
          </p>
        ) : (
          <div className='results-list'>
            {results.map((item) => (
              <SearchResultsRow
                key={item.id}
                name={item.name}
                onViewDetails={
                  () => {
                    window.location.href = `/details/${type}/${item.id}`;
                  }
                }
              />
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default SearchResults;