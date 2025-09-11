// src/components/SearchForm.tsx
import React, { useEffect, useState } from 'react';
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
  const [placeholderState, setPlaceholderState] = useState<string>('e.g. Chewbacca, Yoda, Boba Fett');
  const [searchButtonEnabled, setSearchButtonEnabled] = useState<boolean>(false);
  
  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSearch(searchType, searchTerm);
  };

  useEffect(() => {
    var placeholder = searchType === 'People' ? 'e.g. Chewbacca, Yoda, Boba Fett' : 'e.g. Star Wars, The Empire Strikes Back';
    setPlaceholderState(placeholder);
  }, [searchType]);

 useEffect(() => {
    setSearchButtonEnabled(searchTerm.trim().length > 0);
  }, [searchTerm]);


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
          placeholder={placeholderState}
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="search-input"
        />
        <button type="submit" className="search-button" disabled={!searchButtonEnabled}>
          SEARCH
        </button>
      </form>
    </div>
  );
};

export default SearchForm;