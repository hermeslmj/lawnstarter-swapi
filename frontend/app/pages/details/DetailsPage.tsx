import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router';
import type { PeopleDTO, FilmDTO, ItemDetails, HttpResponse } from '~/types/types';
import { httpRequest } from '~/helpers/HttpHelper';
import './DetailsPage.css';


function isPeopleDTO(obj: any): obj is PeopleDTO {
    return obj && typeof obj === 'object' && 'gender' in obj && 'eyecolor' in obj;
}

function isFilmDTO(obj: any): obj is FilmDTO {
    return obj && typeof obj === 'object' && 'openingCrawl' in obj;
}

const DetailsPage: React.FC = () => {
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
                const data = await httpRequest<HttpResponse>(`http://localhost/api/${type}/show?id=${id}`);
                setDetails(data.content as ItemDetails);
            } catch (err: any) {
                setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        fetchDetails();
    }, [type, id]);

    if (loading) {
        return  <div className="flex items-center justify-center min-h-screen m-auto">
                    <div className="w-10 h-10 border-4 border-green-500 border-t-transparent rounded-full animate-spin"></div>
                </div>;
    }

    if (error) {
        return <div className="details-page-container flex items-center justify-center min-h-[40vh] text-lg text-gray-600">Error: {error}</div>;
    }

    if (!details) {
        return <div className="details-page-containerflex items-center justify-center min-h-[40vh] text-lg text-gray-600">No details found.</div>;
    }

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

    const renderRelated = () => {
        if (isPeopleDTO(details) && details.movies && details.movies.length > 0) {
            return (
                <div className="related-section">
                    <h3>Movies</h3>
                    <ul>
                        {details.movies.map((movie) => {
                            return (
                                <span key={movie.uid}>
                                    <Link to={`/details/films/${movie.uid}`}>
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
                <div className="related-section flex-1">
                    <h3>Characters</h3>
                    <ul>
                        {details.characters.map((character) => {
                            return (
                                <span key={character.uid}>
                                    <Link to={`/details/people/${character.uid}`}>
                                        {character.name}
                                    </Link>
                                </span>
                            );
                        })}
                    </ul>
                </div>
            );
        }
        return null;
    };


    return (
        <div className="details-page-container h-fit w-full max-w-3/5">
            <div>
                <h1 className="main-title">
                    {isFilmDTO(details) ? details.title : details.name}
                </h1>
            </div>
            <div className="content-wrapper flex flex-wrap">
                <div className="details-section">
                    {renderDetails()}
                </div>

                {renderRelated()}
            </div>
            <div className="back-link">
                    <Link to="/" className="back-button">BACK TO SEARCH</Link>
            </div>
        </div>
    );
};

export default DetailsPage;