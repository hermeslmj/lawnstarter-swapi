import React, { useState } from 'react';
import SearchForm from '../components/SearchForm/SearchForm';
import SearchResults from '../components/SearchResults/SearchResults';
import type { SearchType } from '../types/types';
import { useSearch } from '../customHooks/useSearch';

const SearchPage: React.FC = () => {
  const [searchType, setSearchType] = useState<SearchType>('people');
  const [searchTerm, setSearchTerm] = useState<string>('');
  const { results, loading, error, search } = useSearch();

  const handleSearch = (type: SearchType, term: string) => {
    setSearchType(type);
    setSearchTerm(term);
    search(type, term);
  };

  return (
    <>
      <div className="search-section w-full md:w-1/3 mb-6 md:mb-0">
        <SearchForm
          initialSearchType={searchType}
          initialSearchTerm={searchTerm}
          onSearch={handleSearch}
          loading={loading}
        />
      </div>
      <div className="results-section min-h-1/2 w-full md:w-2/3">
        <div className="bg-white p-4">
          <h1 className="text-2xl font-bold text-black-800 border-b border-gray-200">Results</h1>
        </div>
        {loading && (
          <div className="grid h-full grid-cols-1 content-center m-auto">
            <div className="text-center text-gray-600">
              <p className="text-lg mb-2">Searching...</p>
            </div>
          </div>
        )}
        {error && <p className="error-message text-red-600 text-center">{error}</p>}
        {!loading && !error && (
          <SearchResults results={results} type={searchType} />
        )}
      </div>
    </>
  );
};

export default SearchPage;