import type { Route } from "./+types/home";

export function meta({}: Route.MetaArgs) {
  return [
    { title: "New React Router App" },
    { name: "description", content: "Welcome to React Router!" },
  ];
}

import React from "react";
import '../app.css'; 
import SearchPage from "~/pages/SearchPage";


const HomePage: React.FC = () => {


  return (
    <SearchPage />
  );
};
export default HomePage;
