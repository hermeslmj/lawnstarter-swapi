import React, { useState } from 'react';
import SearchForm from '../components/SearchForm/SearchForm';
import SearchResults from '../components/SearchResults/SearchResults';
import type { SearchType, SearchResult, PeopleDTO, FilmDTO, ListDTO, HttpResponse } from '../types/types';
import { httpRequest } from "../helpers/HttpHelper";

const SearchPage: React.FC = () => {
  const [searchType, setSearchType] = useState<SearchType>('people');
  const [searchTerm, setSearchTerm] = useState<string>('');
  const [results, setResults] = useState<SearchResult[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);
  const API_URL = 'http://localhost/api';

  const handleSearch = async (type: SearchType, term: string) => {
    setSearchType(type);
    setSearchTerm(term);
    setLoading(true);
    setError(null);
    setResults([]); 
    

    if(type === 'films') {
      try {
        await httpRequest<HttpResponse>(`${API_URL}/films/?searchTerm=${term}`).then((data) => {
          var resultsArray: SearchResult[] = [];
          console.log(data);
          if (data && Array.isArray(data.content)) {
            data.content.map((film) => {
              resultsArray.push({ id: film.uid, name: film.title });
            });
          }
          setResults(resultsArray);
        }).catch((err) => setError(err.message));
      } catch (err) {
        console.error("Search failed:", err);
        setError("Failed to fetch results. Please try again.");
      } finally {
        setLoading(false);
      }
    }
    if(type === 'people'){
      try {
        await httpRequest<HttpResponse>(`${API_URL}/people/?searchTerm=${term}`)
          .then((data) => {
            var resultsArray: SearchResult[] = [];
            if (data && Array.isArray(data.content)) {
              data.content.map((person) => {
                resultsArray.push({ id: person.uid, name: person.title });
            });
          }
          setResults(resultsArray);
        }).catch((err) => setError(err.message));
      } catch (err) {
        console.error("Search failed:", err);
        setError("Failed to fetch results. Please try again.");
      } finally {
        setLoading(false);
      }
    }
 
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
              {loading && 
                <div className="grid h-full grid-cols-1 content-center m-auto">
                  <div className="text-center text-gray-600">
                    <p className="text-lg mb-2">Searching...</p>
                  </div>
                </div>  
              }
              {error && <p className="error-message text-red-600 text-center">{error}</p>}
              {!loading && !error && (
                <SearchResults results={results} type={searchType}  />
              )}
            </div>
          </>
  );
};
export default SearchPage;