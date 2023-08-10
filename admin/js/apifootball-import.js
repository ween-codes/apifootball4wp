jQuery(document).ready(function ($) {
    $('#country').select2({
        ajax: {
            url: 'https://v3.football.api-sports.io/countries',
            dataType: 'json',
            delay: 250,
            headers: {
                'x-rapidapi-key': apifootball4wp_api_key
            },
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page
                };
            },
            processResults: function (data) {
                let results = [];
                data.response.forEach(e => {
                    results.push({id: e.name, text: e.name});
                });
                console.log(results);
                return {
                    results: results
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });

    $('#season').select2({
        ajax: {
            url: 'https://v3.football.api-sports.io/leagues/seasons',
            dataType: 'json',
            delay: 250,
            headers: {
                'x-rapidapi-key': apifootball4wp_api_key
            },
            processResults: function (data) {
                return {
                    results: data.response
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });
});
