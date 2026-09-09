<?php
// Field schema per dashboard section. Add a new section by adding one entry here —
// the generic admin form and public API both read from this single source of truth.

return [
    'publications' => [
        'label' => 'Publications',
        'title_field' => 'title',
        'fields' => [
            'title'     => ['label' => 'Title', 'type' => 'text', 'required' => true],
            'authors'   => ['label' => 'Authors', 'type' => 'text'],
            'venue'     => ['label' => 'Venue / Journal', 'type' => 'text'],
            'year'      => ['label' => 'Year', 'type' => 'number'],
            'citations' => ['label' => 'Citations', 'type' => 'number'],
            'type'      => ['label' => 'Type', 'type' => 'select', 'options' => ['journal', 'conference', 'book']],
            'doi'       => ['label' => 'DOI (e.g. 10.1000/xyz)', 'type' => 'text'],
        ],
    ],
    'media' => [
        'label' => 'Media & Press',
        'title_field' => 'headline',
        'fields' => [
            'type'        => ['label' => 'Media Type', 'type' => 'select', 'options' => ['Newspaper', 'Magazine', 'Interview', 'TV', 'Online News', 'Podcast']],
            'publication' => ['label' => 'Publication / Source', 'type' => 'text'],
            'date'        => ['label' => 'Date', 'type' => 'text'],
            'headline'    => ['label' => 'Headline', 'type' => 'text', 'required' => true],
            'description' => ['label' => 'Description', 'type' => 'textarea'],
            'category'    => ['label' => 'Category', 'type' => 'text'],
            'url'         => ['label' => 'Source URL', 'type' => 'text'],
            'featured'    => ['label' => 'Featured', 'type' => 'checkbox'],
        ],
    ],
    'achievements' => [
        'label' => 'Achievements',
        'title_field' => 'title',
        'fields' => [
            'icon'  => ['label' => 'Icon (emoji)', 'type' => 'text'],
            'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
            'value' => ['label' => 'Value / Rank', 'type' => 'text'],
            'desc'  => ['label' => 'Description', 'type' => 'textarea'],
        ],
    ],
    'education' => [
        'label' => 'Education',
        'title_field' => 'degree',
        'fields' => [
            'badge'       => ['label' => 'Year / Range Badge', 'type' => 'text'],
            'degree'      => ['label' => 'Degree', 'type' => 'text', 'required' => true],
            'institution' => ['label' => 'Institution', 'type' => 'text'],
            'meta'        => ['label' => 'Meta (CGPA / Result)', 'type' => 'text'],
        ],
    ],
    'experience' => [
        'label' => 'Experience',
        'title_field' => 'role',
        'fields' => [
            'role'    => ['label' => 'Role / Position', 'type' => 'text', 'required' => true],
            'org'     => ['label' => 'Organization', 'type' => 'text'],
            'meta'    => ['label' => 'Location · Duration', 'type' => 'text'],
            'desc'    => ['label' => 'Description', 'type' => 'textarea'],
            'current' => ['label' => 'Current Position', 'type' => 'checkbox'],
        ],
    ],
    'projects' => [
        'label' => 'Projects',
        'title_field' => 'title',
        'fields' => [
            'status'     => ['label' => 'Status', 'type' => 'select', 'options' => ['Active', 'Completed']],
            'title'      => ['label' => 'Title', 'type' => 'text', 'required' => true],
            'desc'       => ['label' => 'Description', 'type' => 'textarea'],
            'tags'       => ['label' => 'Tags (comma separated)', 'type' => 'text'],
            'link_label' => ['label' => 'Link Label', 'type' => 'text'],
            'link_url'   => ['label' => 'Link URL', 'type' => 'text'],
        ],
    ],
    'certifications' => [
        'label' => 'Certifications',
        'title_field' => 'name',
        'fields' => [
            'name'     => ['label' => 'Certificate Name', 'type' => 'text', 'required' => true],
            'org'      => ['label' => 'Issuing Organization', 'type' => 'text'],
            'meta'     => ['label' => 'Date · Credential ID', 'type' => 'text'],
            'link_url' => ['label' => 'Verify Link', 'type' => 'text'],
        ],
    ],
    'gallery' => [
        'label' => 'Gallery',
        'title_field' => 'caption',
        'fields' => [
            'category'  => ['label' => 'Category', 'type' => 'select', 'options' => ['academic', 'research', 'conferences', 'teaching', 'events', 'personal']],
            'image_url' => ['label' => 'Photo', 'type' => 'image'],
            'caption'   => ['label' => 'Caption', 'type' => 'text'],
        ],
    ],
    'skills' => [
        'label' => 'Skills',
        'title_field' => 'category',
        'fields' => [
            'category' => ['label' => 'Category Title', 'type' => 'text', 'required' => true],
            'tags'     => ['label' => 'Skills (comma separated)', 'type' => 'text'],
        ],
    ],
    'teaching' => [
        'label' => 'Teaching',
        'title_field' => 'title',
        'fields' => [
            'badge' => ['label' => 'Type Badge', 'type' => 'text'],
            'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
            'org'   => ['label' => 'Institution / Course', 'type' => 'text'],
            'desc'  => ['label' => 'Description', 'type' => 'textarea'],
        ],
    ],
];
