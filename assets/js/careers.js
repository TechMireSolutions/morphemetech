document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('job-search');
    const categoryFilter = document.getElementById('category-filter');
    const locationFilter = document.getElementById('location-filter');
    const employmentTypeFilter = document.getElementById('employment-type-filter');
    const jobListingsContainer = document.getElementById('job-listings');

    const fetchJobs = () => {
        const searchQuery = searchInput.value;
        const category = categoryFilter.value;
        const location = locationFilter.value;
        const employmentType = employmentTypeFilter.value;

        // AJAX request banane ke liye URL
        const url = `fetch_jobs.php?search=${encodeURIComponent(searchQuery)}&category=${encodeURIComponent(category)}&location=${encodeURIComponent(location)}&employment_type=${encodeURIComponent(employmentType)}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                jobListingsContainer.innerHTML = ''; // Purani listings hatayein

                if (Object.keys(data).length === 0) {
                    jobListingsContainer.innerHTML = '<p>No jobs found matching your criteria.</p>';
                    return;
                }

                // Nayi listings ko render karein
                for (const categoryName in data) {
                    const categorySection = document.createElement('div');
                    categorySection.className = 'job-category-section';
                    
                    const categoryTitle = document.createElement('h2');
                    categoryTitle.textContent = categoryName;
                    categorySection.appendChild(categoryTitle);

                    data[categoryName].forEach(job => {
                        const jobLink = document.createElement('a');
                        jobLink.href = `job-details.php?id=${job.id}`;
                        jobLink.className = 'job-listing-item';

                        jobLink.innerHTML = `
                            <div class="job-info">
                                <h3>${job.title}</h3>
                                <p><span>${job.employment_type}</span> | <span>${job.location}</span></p>
                            </div>
                            <span class="arrow-icon">&gt;</span>
                        `;
                        categorySection.appendChild(jobLink);
                    });
                    jobListingsContainer.appendChild(categorySection);
                }
            })
            .catch(error => {
                console.error('Error fetching jobs:', error);
            });
    };

    // Filters par event listeners lagayein
    searchInput.addEventListener('input', fetchJobs);
    categoryFilter.addEventListener('change', fetchJobs);
    locationFilter.addEventListener('change', fetchJobs);
    employmentTypeFilter.addEventListener('change', fetchJobs);
});