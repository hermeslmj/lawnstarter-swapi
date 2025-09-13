export type SearchType = 'people' | 'films';

export interface SearchResult {
  id: string;
  name: string;
}

export type PersonFilmsData = {
  uid: string;
  title: string;
};

export type PeopleDTO = {
  uid: string;
  name: string;
  gender: string;
  eyecolor: string;
  haircolor: string;
  height: string;
  mass: string;
  birthYear: string;
  movies: PersonFilmsData[];
};

export type FilmCharactersData = {
  uid: string;
  name: string;
};

export type FilmDTO = {
  uid: string;
  title: string;
  openingCrawl: string;
  characters: FilmCharactersData[];
}

export type ListDTO = {
  uid: string;
  title: string;
};

export type SearchResultsProps = {
  results: SearchResult[];
  type: 'people' | 'films';
}

export type SearchResultsRowProps = {
  name: string;
  onViewDetails: () => void;
}

export type SearchFormProps = {
  initialSearchType: SearchType;
  initialSearchTerm: string;
  onSearch: (type: SearchType, term: string) => void;
  loading?: boolean;
}

export type ItemDetails = PeopleDTO | FilmDTO;
