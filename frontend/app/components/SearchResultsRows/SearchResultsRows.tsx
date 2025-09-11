// src/components/SearchResultsRow.tsx
import React from 'react';
import './SearchResultsRows.css'; // Create this CSS file

interface SearchResultsRowProps {
  name: string;
  onViewDetails: () => void; // Function to call when "See Details" is clicked
}

const SearchResultsRow: React.FC<SearchResultsRowProps> = ({ name, onViewDetails }) => {
  return (
    <div className="search-results-row">
      <span className="item-name">{name}</span>
      <button className="details-button" onClick={onViewDetails}>
        SEE DETAILS
      </button>
    </div>
  );
};

export default SearchResultsRow;