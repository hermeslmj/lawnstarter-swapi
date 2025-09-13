import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router';
import type { PeopleDTO, FilmDTO } from '~/types/types';
import './DetailsPage.css';

type ItemDetails = PeopleDTO | FilmDTO;

function isPeopleDTO(obj: any): obj is PeopleDTO {
  return obj && typeof obj === 'object' && 'gender' in obj && 'eyecolor' in obj;
}

function isFilmDTO(obj: any): obj is FilmDTO {
  return obj && typeof obj === 'object' && 'openingCrawl' in obj;
}

const DetailsPage: React.FC = () => {
    // useParams returns string or undefined, so we type them explicitly
    const { type, id } = useParams<{ type: string; id: string }>();
    const [details, setDetails] = useState<ItemDetails | null>(null);
    const [loading, setLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchDetails = async () => {
            if (!type || !id) {
                setError("Invalid URL parameters.");
                setLoading(false);
                return;
            }

            setLoading(true);
            setError(null);
            try {
                const response = await fetch(`http://localhost/api/${type}/show?id=${id}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data: ItemDetails = await response.json();
                setDetails(data);
            } catch (err: any) { // Type 'any' for err in catch block if not specific error type is known
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        fetchDetails();
    }, [type, id]); // Re-run effect if type or id changes

    if (loading) {
        return <div className="details-page-container flex items-center justify-center min-h-[40vh] text-lg text-gray-600">Loading...</div>;
    }

    if (error) {
        return <div className="details-page-container flex items-center justify-center min-h-[40vh] text-lg text-gray-600">Error: {error}</div>;
    }

    if (!details) {
        return <div className="details-page-container flex items-center justify-center min-h-[40vh] text-lg text-gray-600">No details found.</div>;
    }


    // Function to render details based on the type
    const renderDetails = () => {
        if (isPeopleDTO(details)) {
            return (
                <>
                    <h3>Details</h3>
                    <ul>
                        <li>Birth Year: {details.birthYear}</li>
                        <li>Gender: {details.gender}</li>
                        <li>Eye Color: {details.eyecolor}</li>
                        <li>Hair Color: {details.haircolor}</li>
                        <li>Height: {details.height} cm</li>
                        <li>Mass: {details.mass} kg</li>
                    </ul>
                </>
            );
        } else if (isFilmDTO(details)) {
            return (
                <>
                    <h3>Opening Crawl</h3>
                    <ul>
                        <li><pre>{details.openingCrawl}</pre></li>
                    </ul>
                </>
            );
        }
        return null;
    };
    // Function to render related items (e.g., movies for people, or characters/planets for films)
    const renderRelated = () => {
        if (isPeopleDTO(details) && details.movies && details.movies.length > 0) {
            return (
                <div className="related-section">
                    <h3>Movies</h3>
                    <ul>
                        {details.movies.map((movie) => {
                            return (
                                <span  key={movie.uid}>
                                    <Link className='comma-list' to={`/details/films/${movie.uid}`}>
                                        {movie.title}
                                    </Link>
                                </span>
                            );
                        })}
                    </ul>
                </div>
            );
        } else if (isFilmDTO(details) && details.characters && details.characters.length > 0) {
            return (
                <div className="related-section">
                    <h3>Characters</h3>
                    <ul>
                        {details.characters.map((character) => {
                            return (
                                <span key={character.uid}>
                                    <Link className='comma-list' to={`/details/people/${character.uid}`}>
                                        {character.name}
                                    </Link>
                                </span>
                            );
                        })}
                    </ul>
                </div>
            );
        }
        // Add more conditions for films related to planets, starships, etc.
        return null;
    };


    return (
        <div className="details-page-container">
            <div>
                <h1 className="main-title">
                    {isFilmDTO(details) ? details.title : details.name}
                </h1>    
            </div>
            <div className="content-wrapper">
                <div className="details-section">
                    {renderDetails()}
                </div>

                { renderRelated()}
            </div>

            <Link to="/" className="back-button">BACK TO SEARCH</Link>
        </div>
    );
};

export default DetailsPage;