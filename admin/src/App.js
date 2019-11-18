import React from "react";
import { HydraAdmin, ResourceGuesser } from "@api-platform/admin";

export default () => (
    <HydraAdmin entrypoint="http://localhost:8096/api">
        <ResourceGuesser name="users" />
        <ResourceGuesser name="groups" />
        <ResourceGuesser name="skills" />
        <ResourceGuesser name="languages" />
        <ResourceGuesser name="bindings" />
        <ResourceGuesser name="series" />
        <ResourceGuesser name="audience" />
        <ResourceGuesser name="project" />
        <ResourceGuesser name="product" />
    </HydraAdmin>
);
