import type { Route } from "./+types/home";

export function meta({}: Route.MetaArgs) {
  return [
    { title: "New React Router App" },
    { name: "description", content: "Welcome to React Router!" },
  ];
}

import React, { useState } from 'react';
import SearchForm from '../components/SearchForm/SearchForm';
import SearchResults from '../components/SearchResults/SearchResults';
import type { SearchType, SearchResult } from '../types/types';
import '../app.css'; // You can keep the main layout CSS here or in App.css

const HomePage: React.FC = () => {
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
    setResults([]); // Clear previous results

    try {
      // Simulate an API call
      console.log(`Simulating API call for ${term} in ${type}...`);
      //await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate network delay
        const response = await fetch('http://localhost/api/films/show?id=3'); // Replace with your API endpoint
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();

      // In a real app, you'd fetch data here
      // const response = await fetch(`/api/${type.toLowerCase()}?q=${term}`);
      // if (!response.ok) throw new Error('Network response was not ok');
      // const data = await response.json();
      // setResults(data); // Assuming data is an array of SearchResult

      // For this example, we always return zero matches
      setResults([]);

    } catch (err) {
      console.error("Search failed:", err);
      setError("Failed to fetch results. Please try again.");
    } finally {
      setLoading(false);
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
          {loading && <p>Loading results...</p>}
          {error && <p className="error-message">{error}</p>}
          {!loading && !error && (
            <SearchResults results={results} />
          )}
        </div>
      </div>
    </div>
  );
};
export default HomePage;
