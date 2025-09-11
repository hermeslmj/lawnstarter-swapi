import React, { useState } from 'react';
import SearchForm from '../components/SearchForm/SearchForm';
import SearchResults from '../components/SearchResults/SearchResults';
import type { SearchType, SearchResult, PeopleDTO, FilmDTO } from '../types/types';
import { httpRequest } from "~/helpers/HttpHelper";

const SearchPage: React.FC = () => {
  const [searchType, setSearchType] = useState<SearchType>('People');
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
    

    if(type === 'Movies') {
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
    if(type === 'People'){
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
    <div className="app-container">
      <header className="header">
        <h1>SWStarter</h1>
      </header>
      <div className="main-content">
        <div className="search-section">
          <SearchForm
            initialSearchType={searchType}
            initialSearchTerm={searchTerm}
            onSearch={handleSearch}
          />
        </div>
        <div className="results-section">
          {loading && <p>Searching...</p>}
          {error && <p className="error-message">{error}</p>}
          {!loading && !error && (
            <SearchResults results={results} />
          )}
        </div>
      </div>
    </div>
  );
};
export default SearchPage;