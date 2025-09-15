import React from 'react';
import { useNavigate } from 'react-router';
import type { SearchResultsProps } from '../../types/types';
import SearchResultsRow from '../SearchResultsRows/SearchResultsRows';

const SearchResults: React.FC<SearchResultsProps> = ({ results, type }) => {
  const navigate = useNavigate();

  return (
<>     
        {results.length === 0 ? (
          <div className="grid h-full grid-cols-1 content-center m-auto text-center text-gray-600">
              <div className="m-auto">
                <p className="text-md ">There are zero matches.</p>
                <p className="text-md">Use the form to search for People or Movies.</p>
              </div>
            
          </div>
          
        ) : (
           <div className="flex flex-col container mx-auto px-4">
            {results.map((item) => (
              <SearchResultsRow
                key={item.id}
                name={item.name}
                onViewDetails={() => navigate(`/details/${type}/${item.id}`)}
              />
            ))}
          </div>
        )}
     
    </>
  );
};

export default SearchResults;