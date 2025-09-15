import { type RouteConfig, index, layout, route } from "@react-router/dev/routes";


 export default [
    layout("./pages/layout/PageLayout.tsx", [
    index("pages/SearchPage.tsx"),
    route("details/:type/:id", "pages/details/DetailsPage.tsx"),
    route("statistics", "pages/statistics/StatisticsPage.tsx"),
  ]),
] satisfies RouteConfig;