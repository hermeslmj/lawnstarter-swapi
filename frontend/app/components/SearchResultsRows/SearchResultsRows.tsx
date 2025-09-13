import React from 'react';
import './SearchResultsRows.css';
import type { SearchResultsRowProps } from '~/types/types';

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