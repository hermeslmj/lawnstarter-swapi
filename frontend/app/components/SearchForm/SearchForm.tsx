// src/components/SearchForm.tsx
import React, { useState } from 'react';
import type { SearchType } from '../../types/types';
import './SearchForm.css'; // Assuming you'll create a SearchForm.css

interface SearchFormProps {
  initialSearchType: SearchType;
  initialSearchTerm: string;
  onSearch: (type: SearchType, term: string) => void;
}

const SearchForm: React.FC<SearchFormProps> = ({
  initialSearchType,
  initialSearchTerm,
  onSearch,
}) => {
  const [searchType, setSearchType] = useState<SearchType>(initialSearchType);
  const [searchTerm, setSearchTerm] = useState<string>(initialSearchTerm);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSearch(searchType, searchTerm);
  };

  return (
    <div className="search-form-container">
      <h2>What are you searching for?</h2>
      <form onSubmit={handleSubmit}>
        <div className="radio-group">
          <label>
            <input
              type="radio"
              value="People"
              checked={searchType === 'People'}
              onChange={() => setSearchType('People')}
            />
            People
          </label>
          <label>
            <input
              type="radio"
              value="Movies"
              checked={searchType === 'Movies'}
              onChange={() => setSearchType('Movies')}
            />
            Movies
          </label>
        </div>
        <input
          type="text"
          placeholder="e.g. Chewbacca, Yoda, Boba Fett"
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="search-input"
        />
        <button type="submit" className="search-button">
          SEARCH
        </button>
      </form>
    </div>
  );
};

export default SearchForm;