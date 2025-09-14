import React from 'react';
import { useNavigate } from 'react-router';
import type { SearchResultsProps } from '../../types/types';
import SearchResultsRow from '../SearchResultsRows/SearchResultsRows';

const SearchResults: React.FC<SearchResultsProps> = ({ results, type }) => {
  const navigate = useNavigate();

  return (
    <div className="flex flex-col">
      <div className="bg-white p-4">
        <h1 className="text-2xl font-bold text-black-800 border-b border-gray-200">Results</h1>
      </div>
      <div className="flex flex-col container mx-auto px-4">
        {results.length === 0 ? (
          <div className="grid grid-cols-1 content-center m-auto gap-4">
            <div className="text-center text-gray-600">
              <p className="text-lg mb-2">There are zero matches.</p>
              <p className="text-base">Use the form to search for People or Movies.</p>
            </div>
          </div>
        ) : (
          <div>
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