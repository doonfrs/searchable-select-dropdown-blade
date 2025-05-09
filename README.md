# Searchable Select Component

A reusable searchable select dropdown component with support for filtering options.

## Features

- Searchable dropdown with real-time filtering
- Support for dark/light mode
- Error state styling
- Required/disabled states
- Custom placeholder text
- Custom "no results" message
- Keyboard navigation support:
  - Start typing to open dropdown and filter
  - Down arrow key to open the dropdown
  - Arrow keys to navigate through options
  - Enter or Space to select focused option
  - Escape key to close dropdown

## Usage

```blade
<x-searchable-select
    name="country"
    id="country" 
    :options="$countries"
    placeholder="{{ __('Select a country') }}"
    searchPlaceholder="{{ __('Search country') }}"
    noResults="{{ __('No results found') }}"
    :error="$errors->has('country')"
    :selected="old('country', $model->country ?? '')"
    :required="true"
/>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| options | Array | [] | Array of options in format [{id: 1, name: 'Option 1'}, ...] |
| name | String | '' | Form field name |
| id | String | null | Input ID (defaults to name if not provided) |
| selected | String | '' | Currently selected value |
| placeholder | String | '' | Placeholder text for the dropdown |
| searchPlaceholder | String | '' | Placeholder text for the search field |
| noResults | String | '' | Text to show when no results are found |
| required | Boolean | false | Whether the field is required |
| disabled | Boolean | false | Whether the field is disabled |
| error | Boolean | false | Whether the field has an error |


## Translation

Make sure to add translation keys for the placeholder, search placeholder and "no results" message:

```json
{
    "Select a country": "Select a country",
    "Search country": "Search country",
    "No results found": "No results found"
}
```
