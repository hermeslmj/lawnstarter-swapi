import React from 'react';
import { useNavigate } from 'react-router';
import type { SearchResultsProps } from '../../types/types';
import SearchResultsRow from '../SearchResultsRows/SearchResultsRows';
import './SearchResults.css';

const SearchResults: React.FC<SearchResultsProps> = ({ results, type }) => {
  const navigate = useNavigate();

  return (
    <div className="search-results-container">
      <div className='results-header'>
        <h1>Results</h1>
      </div>
      <div className="results-content">
        {results.length === 0 ? (
          <div className="no-matches flex items-center justify-center min-h-[40vh] text-lg text-gray-600">
            There are zero matches.
            <br />
            Use the form to search for People or Movies.
          </div>
        ) : (
          <div className='results-list'>
            {results.map((item) => (
              <SearchResultsRow
                key={item.id}
                name={item.name}
                onViewDetails={() => navigate(`/details/${type}/${item.id}`)}
              />
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default SearchResults;