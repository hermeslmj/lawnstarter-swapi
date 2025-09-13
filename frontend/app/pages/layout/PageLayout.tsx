import React  from 'react';
import Header from '../../components/Header/Header';
import { Outlet } from 'react-router';


const PageLayout = () => (
    <div className="app-container min-h-screen bg-gray-100 flex flex-col">
        <Header />
        <div className="main-content flex-1 flex flex-col md:flex-row gap-6 p-4 md:p-8">
        <Outlet />
        </div>
    </div>
);

export default PageLayout;