export async function getProjects() {
    const response = await fetch('/api/projects');

    if (!response.ok) {
        throw new Error(`Projects API returned ${response.status}`);
    }

    const json = await response.json();

    console.log('Projects API response:', json);

    if (!Array.isArray(json.data)) {
        throw new Error('Projects API response does not contain a data array.');
    }

    return json.data;
}

export async function getProject(id) {
    const response = await fetch(`/api/projects/${id}`);

    if (!response.ok) {
        throw new Error(`Project API returned ${response.status}`);
    }

    const json = await response.json();

    console.log('Project detail API response:', json);

    if (!json.data) {
        throw new Error('Project API response does not contain project data.');
    }

    return json.data;
}