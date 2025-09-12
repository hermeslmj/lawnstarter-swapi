import React, { useState } from 'react';
import SearchForm from '../components/SearchForm/SearchForm';
import SearchResults from '../components/SearchResults/SearchResults';
import type { SearchType, SearchResult, PeopleDTO, FilmDTO } from '../types/types';
import { httpRequest } from "~/helpers/HttpHelper";

const SearchPage: React.FC = () => {
  const [searchType, setSearchType] = useState<SearchType>('people');
  const [searchTerm, setSearchTerm] = useState<string>('');
  const [results, setResults] = useState<SearchResult[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);

  const handleSearch = async (type: SearchType, term: string) => {
    setSearchType(type);
    setSearchTerm(term);
    setLoading(true);
    setError(null);
    setResults([]); 
    

    if(type === 'films') {
      try {
        //TODO: url should be in a config file
        await httpRequest<FilmDTO[]>(`http://localhost/api/films/?searchTerm=${term}`).then((data) => {
          var resultsArray: SearchResult[] = [];
          if (data) {
            data.map((film) => {
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
      try 
      {
        //TODO: url should be in a config file
        await httpRequest<PeopleDTO[]>(`http://localhost/api/people/?searchTerm=${term}`)
        .then((data) => {
          var resultsArray: SearchResult[] = [];
          if (data) {
            data.map((person) => {
              resultsArray.push({ id: person.uid, name: person.name });
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
        <div className="app-container min-h-screen bg-gray-100 flex flex-col">
          <header className="header bg-blue-900 text-white py-4 px-6">
            <h1 className="text-2xl md:text-3xl font-bold text-center">SWStarter</h1>
          </header>
          <div className="main-content flex-1 flex flex-col md:flex-row gap-6 p-4 md:p-8">
            <div className="search-section w-full md:w-1/3 mb-6 md:mb-0">
              <SearchForm
                initialSearchType={searchType}
                initialSearchTerm={searchTerm}
                onSearch={handleSearch}
              />
            </div>
            <div className="results-section w-full md:w-2/3">
              {loading && <p className="text-center text-gray-500">Searching...</p>}
              {error && <p className="error-message text-red-600 text-center">{error}</p>}
              {!loading && !error && (
                <SearchResults results={results} type={searchType} />
              )}
            </div>
          </div>
        </div>
  );
};
export default SearchPage;