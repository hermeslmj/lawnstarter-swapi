// src/types.ts

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

