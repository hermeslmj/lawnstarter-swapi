import { type RouteConfig, index, route } from "@react-router/dev/routes";

export default [
    index("routes/home.tsx"),
    route("details/:type/:id", "pages/details/DetailsPage.tsx"),
] satisfies RouteConfig;
