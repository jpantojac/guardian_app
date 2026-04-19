<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>API de GuardianApp</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://guardianapp.test";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.9.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.9.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-analisis-espacial-geojson" class="tocify-header">
                <li class="tocify-item level-1" data-unique="analisis-espacial-geojson">
                    <a href="#analisis-espacial-geojson">Análisis Espacial (GeoJSON)</a>
                </li>
                                    <ul id="tocify-subheader-analisis-espacial-geojson" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="analisis-espacial-geojson-GETapi-geojson">
                                <a href="#analisis-espacial-geojson-GETapi-geojson">Obtener incidentes en GeoJSON.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="analisis-espacial-geojson-GETapi-localidades-geojson">
                                <a href="#analisis-espacial-geojson-GETapi-localidades-geojson">Obtener polígonos de localidades (GeoJSON).</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-autenticacion" class="tocify-header">
                <li class="tocify-item level-1" data-unique="autenticacion">
                    <a href="#autenticacion">Autenticación</a>
                </li>
                                    <ul id="tocify-subheader-autenticacion" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="autenticacion-POSTapi-register">
                                <a href="#autenticacion-POSTapi-register">Registro de nuevo usuario.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacion-POSTapi-login">
                                <a href="#autenticacion-POSTapi-login">Inicio de sesión.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacion-POSTapi-logout">
                                <a href="#autenticacion-POSTapi-logout">Cerrar sesión.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacion-GETapi-user">
                                <a href="#autenticacion-GETapi-user">Obtener perfil autenticado.</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-POSTapi-incidents--incident_id--comments">
                                <a href="#endpoints-POSTapi-incidents--incident_id--comments">POST api/incidents/{incident_id}/comments</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-comments--comment_id--reactions">
                                <a href="#endpoints-POSTapi-comments--comment_id--reactions">POST api/comments/{comment_id}/reactions</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-incidentes" class="tocify-header">
                <li class="tocify-item level-1" data-unique="incidentes">
                    <a href="#incidentes">Incidentes</a>
                </li>
                                    <ul id="tocify-subheader-incidentes" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="incidentes-POSTapi-incidents">
                                <a href="#incidentes-POSTapi-incidents">Reportar nuevo incidente.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="incidentes-GETapi-incidents">
                                <a href="#incidentes-GETapi-incidents">Listado paginado de incidentes.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="incidentes-GETapi-incidents--incident_id-">
                                <a href="#incidentes-GETapi-incidents--incident_id-">Detalles completos del incidente.</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: April 6, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>Documentación oficial MVP v1.0</p>
<aside>
    <strong>Base URL</strong>: <code>http://guardianapp.test</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include an <strong><code>Authorization</code></strong> header with the value <strong><code>"Bearer {YOUR_AUTH_KEY}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>You can retrieve your token by visiting your dashboard and clicking <b>Generate API token</b>.</p>

        <h1 id="analisis-espacial-geojson">Análisis Espacial (GeoJSON)</h1>

    <p>APIs que exponen datos en formato estándar GeoJSON (RFC 7946) para ser consumidos por mapas.</p>

                                <h2 id="analisis-espacial-geojson-GETapi-geojson">Obtener incidentes en GeoJSON.</h2>

<p>
</p>

<p>Retorna una colección de características GeoJSON (<code>FeatureCollection</code>) que representan los incidentes registrados en el sistema, ideal para ser consumida directamente por mapas web y sistemas SIG. Permite filtrar los resultados temporalmente o por categoría.</p>

<span id="example-requests-GETapi-geojson">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://guardianapp.test/api/geojson?days=7&amp;year=2026&amp;month=3&amp;start_date=2026-03-01&amp;end_date=2026-03-31&amp;category_id=1&amp;categories[]=1&amp;categories[]=2" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/geojson"
);

const params = {
    "days": "7",
    "year": "2026",
    "month": "3",
    "start_date": "2026-03-01",
    "end_date": "2026-03-31",
    "category_id": "1",
    "categories[0]": "1",
    "categories[1]": "2",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-geojson">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;type&quot;: &quot;FeatureCollection&quot;,
    &quot;features&quot;: [
        {
            &quot;type&quot;: &quot;Feature&quot;,
            &quot;geometry&quot;: {
                &quot;type&quot;: &quot;Point&quot;,
                &quot;coordinates&quot;: [
                    -74.0721,
                    4.711
                ]
            },
            &quot;properties&quot;: {
                &quot;id&quot;: 1001,
                &quot;category&quot;: &quot;Hurto a personas&quot;,
                &quot;color&quot;: &quot;#ff0000&quot;,
                &quot;icon&quot;: &quot;hurto_icon&quot;,
                &quot;description&quot;: &quot;Robo con arma blanca en la calle 80&quot;,
                &quot;location_description&quot;: &quot;Cerca a la estaci&oacute;n de Transmilenio&quot;,
                &quot;status&quot;: &quot;reported&quot;,
                &quot;privacy_level&quot;: &quot;IDENTIFIED&quot;,
                &quot;reporter_name&quot;: &quot;Juan Perez&quot;,
                &quot;localidad&quot;: &quot;Engativ&aacute;&quot;,
                &quot;photos&quot;: [
                    &quot;http://guardianapp.test/storage/incident_photos/example.jpg&quot;
                ],
                &quot;created_at&quot;: &quot;2026-04-01T14:30:00Z&quot;
            }
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-geojson" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-geojson"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-geojson"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-geojson" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-geojson">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-geojson" data-method="GET"
      data-path="api/geojson"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-geojson', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-geojson"
                    onclick="tryItOut('GETapi-geojson');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-geojson"
                    onclick="cancelTryOut('GETapi-geojson');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-geojson"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/geojson</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-geojson"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-geojson"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>days</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="days"                data-endpoint="GETapi-geojson"
               value="7"
               data-component="query">
    <br>
<p>Filtrar incidentes de los últimos N días. Example: <code>7</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>year</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="year"                data-endpoint="GETapi-geojson"
               value="2026"
               data-component="query">
    <br>
<p>Filtrar por año. Example: <code>2026</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>month</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="month"                data-endpoint="GETapi-geojson"
               value="3"
               data-component="query">
    <br>
<p>Filtrar por mes (1-12). Example: <code>3</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>start_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="start_date"                data-endpoint="GETapi-geojson"
               value="2026-03-01"
               data-component="query">
    <br>
<p>date Filtrar incidentes ocurridos desde esta fecha de inicio. Example: <code>2026-03-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>end_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="end_date"                data-endpoint="GETapi-geojson"
               value="2026-03-31"
               data-component="query">
    <br>
<p>date Filtrar incidentes ocurridos hasta esta fecha de fin. Example: <code>2026-03-31</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>category_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="category_id"                data-endpoint="GETapi-geojson"
               value="1"
               data-component="query">
    <br>
<p>Filtrar por ID de la categoría del delito. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>categories</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="categories[0]"                data-endpoint="GETapi-geojson"
               data-component="query">
        <input type="text" style="display: none"
               name="categories[1]"                data-endpoint="GETapi-geojson"
               data-component="query">
    <br>
<p>Filtrar múltiples IDs de categoría.</p>
            </div>
                </form>

                    <h2 id="analisis-espacial-geojson-GETapi-localidades-geojson">Obtener polígonos de localidades (GeoJSON).</h2>

<p>
</p>

<p>Retorna la capa cartográfica de las localidades de la ciudad en formato <code>FeatureCollection</code> GeoJSON.
Útil para renderizar los límites jurisdiccionales sociodemográficos sobre el mapa principal.</p>

<span id="example-requests-GETapi-localidades-geojson">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://guardianapp.test/api/localidades-geojson" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/localidades-geojson"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-localidades-geojson">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;type&quot;: &quot;FeatureCollection&quot;,
    &quot;features&quot;: [
        {
            &quot;type&quot;: &quot;Feature&quot;,
            &quot;properties&quot;: {
                &quot;id&quot;: 1,
                &quot;nombre&quot;: &quot;Usaqu&eacute;n&quot;
            },
            &quot;geometry&quot;: {
                &quot;type&quot;: &quot;MultiPolygon&quot;,
                &quot;coordinates&quot;: [
                    [
                        [
                            [
                                -74.012,
                                4.789
                            ],
                            [
                                -74.015,
                                4.792
                            ]
                        ]
                    ]
                ]
            }
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-localidades-geojson" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-localidades-geojson"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-localidades-geojson"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-localidades-geojson" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-localidades-geojson">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-localidades-geojson" data-method="GET"
      data-path="api/localidades-geojson"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-localidades-geojson', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-localidades-geojson"
                    onclick="tryItOut('GETapi-localidades-geojson');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-localidades-geojson"
                    onclick="cancelTryOut('GETapi-localidades-geojson');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-localidades-geojson"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/localidades-geojson</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-localidades-geojson"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-localidades-geojson"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="autenticacion">Autenticación</h1>

    <p>APIs para gestionar el registro, inicio de sesión y perfil de usuarios usando Laravel Sanctum.</p>

                                <h2 id="autenticacion-POSTapi-register">Registro de nuevo usuario.</h2>

<p>
</p>

<p>Permite la creación de un nuevo perfil ciudadano en la plataforma. Al completarse, retorna las credenciales de acceso (Token Sanctum) en formato Bearer para autenticar el dispositivo de forma segura.</p>

<span id="example-requests-POSTapi-register">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://guardianapp.test/api/register" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Maria Torres\",
    \"email\": \"mtorres@example.com\",
    \"password\": \"S3cur3P@ssw0rd!\",
    \"password_confirmation\": \"S3cur3P@ssw0rd!\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/register"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Maria Torres",
    "email": "mtorres@example.com",
    "password": "S3cur3P@ssw0rd!",
    "password_confirmation": "S3cur3P@ssw0rd!"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-register">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;access_token&quot;: &quot;1|abcdef1234567890&quot;,
    &quot;token_type&quot;: &quot;Bearer&quot;,
    &quot;user&quot;: {
        &quot;id&quot;: 145,
        &quot;name&quot;: &quot;Maria Torres&quot;,
        &quot;email&quot;: &quot;mtorres@example.com&quot;,
        &quot;role&quot;: &quot;user&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-register" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-register"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-register"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-register" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-register">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-register" data-method="POST"
      data-path="api/register"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-register', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-register"
                    onclick="tryItOut('POSTapi-register');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-register"
                    onclick="cancelTryOut('POSTapi-register');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-register"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-register"
               value="Maria Torres"
               data-component="body">
    <br>
<p>Nombre completo del ciudadano o seudónimo de registro. Example: <code>Maria Torres</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-register"
               value="mtorres@example.com"
               data-component="body">
    <br>
<p>Correo electrónico único válido, servirá como usuario de acceso. Example: <code>mtorres@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-register"
               value="S3cur3P@ssw0rd!"
               data-component="body">
    <br>
<p>Contraseña segura (mínimo 8 caracteres). Example: <code>S3cur3P@ssw0rd!</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password_confirmation"                data-endpoint="POSTapi-register"
               value="S3cur3P@ssw0rd!"
               data-component="body">
    <br>
<p>Confirmación idéntica a la contraseña enviada en <code>password</code>. Example: <code>S3cur3P@ssw0rd!</code></p>
        </div>
        </form>

                    <h2 id="autenticacion-POSTapi-login">Inicio de sesión.</h2>

<p>
</p>

<p>Autentica a un usuario existente mediante sus credenciales (correo electrónico y contraseña). El sistema emite un token de acceso temporal mediante Laravel Sanctum, que el dispositivo móvil o frontend enviará en cabeceras HTTP subsecuentes.</p>

<span id="example-requests-POSTapi-login">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://guardianapp.test/api/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"juanperez@example.com\",
    \"password\": \"M1P4ssw0rd!\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "juanperez@example.com",
    "password": "M1P4ssw0rd!"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-login">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;access_token&quot;: &quot;2|qwerty0987654321&quot;,
    &quot;token_type&quot;: &quot;Bearer&quot;,
    &quot;user&quot;: {
        &quot;id&quot;: 12,
        &quot;name&quot;: &quot;Juan Perez&quot;,
        &quot;email&quot;: &quot;juanperez@example.com&quot;,
        &quot;role&quot;: &quot;user&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Las credenciales proporcionadas son incorrectas.&quot;,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;Las credenciales proporcionadas son incorrectas.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-login" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-login"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-login">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-login" data-method="POST"
      data-path="api/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-login', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-login"
                    onclick="tryItOut('POSTapi-login');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-login"
                    onclick="cancelTryOut('POSTapi-login');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-login"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-login"
               value="juanperez@example.com"
               data-component="body">
    <br>
<p>Correo registrado del usuario. Example: <code>juanperez@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-login"
               value="M1P4ssw0rd!"
               data-component="body">
    <br>
<p>Contraseña vinculada a su perfil de seguridad. Example: <code>M1P4ssw0rd!</code></p>
        </div>
        </form>

                    <h2 id="autenticacion-POSTapi-logout">Cerrar sesión.</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Invalida el token actual (Bearer Token) del usuario, finalizando su sesión activa en el dispositivo invocador. Mejora la seguridad tras desvincular un cliente específico.</p>

<span id="example-requests-POSTapi-logout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://guardianapp.test/api/logout" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/logout"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-logout">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Sesi&oacute;n cerrada correctamente&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-logout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-logout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-logout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-logout" data-method="POST"
      data-path="api/logout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-logout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-logout"
                    onclick="tryItOut('POSTapi-logout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-logout"
                    onclick="cancelTryOut('POSTapi-logout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-logout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-logout"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="autenticacion-GETapi-user">Obtener perfil autenticado.</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna la información en detalle asociada al titular del token Sanctum actual, confirmando que la sesión siga activa y las credenciales sean correctas.</p>

<span id="example-requests-GETapi-user">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://guardianapp.test/api/user" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/user"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-user">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 12,
    &quot;name&quot;: &quot;Juan Perez&quot;,
    &quot;email&quot;: &quot;juanperez@example.com&quot;,
    &quot;email_verified_at&quot;: null,
    &quot;role&quot;: &quot;user&quot;,
    &quot;created_at&quot;: &quot;2026-03-01T21:00:00.000000Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-user" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-user"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-user"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-user">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-user" data-method="GET"
      data-path="api/user"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-user', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-user"
                    onclick="tryItOut('GETapi-user');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-user"
                    onclick="cancelTryOut('GETapi-user');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-user"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/user</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-user"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-POSTapi-incidents--incident_id--comments">POST api/incidents/{incident_id}/comments</h2>

<p>
</p>



<span id="example-requests-POSTapi-incidents--incident_id--comments">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://guardianapp.test/api/incidents/1/comments" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"content\": \"b\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/incidents/1/comments"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "content": "b"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-incidents--incident_id--comments">
</span>
<span id="execution-results-POSTapi-incidents--incident_id--comments" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-incidents--incident_id--comments"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-incidents--incident_id--comments"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-incidents--incident_id--comments" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-incidents--incident_id--comments">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-incidents--incident_id--comments" data-method="POST"
      data-path="api/incidents/{incident_id}/comments"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-incidents--incident_id--comments', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-incidents--incident_id--comments"
                    onclick="tryItOut('POSTapi-incidents--incident_id--comments');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-incidents--incident_id--comments"
                    onclick="cancelTryOut('POSTapi-incidents--incident_id--comments');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-incidents--incident_id--comments"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/incidents/{incident_id}/comments</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-incidents--incident_id--comments"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-incidents--incident_id--comments"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>incident_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="incident_id"                data-endpoint="POSTapi-incidents--incident_id--comments"
               value="1"
               data-component="url">
    <br>
<p>The ID of the incident. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>content</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="content"                data-endpoint="POSTapi-incidents--incident_id--comments"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 1000 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>parent_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="parent_id"                data-endpoint="POSTapi-incidents--incident_id--comments"
               value=""
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the comments table.</p>
        </div>
        </form>

                    <h2 id="endpoints-POSTapi-comments--comment_id--reactions">POST api/comments/{comment_id}/reactions</h2>

<p>
</p>



<span id="example-requests-POSTapi-comments--comment_id--reactions">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://guardianapp.test/api/comments/16/reactions" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"type\": \"support\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/comments/16/reactions"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "type": "support"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-comments--comment_id--reactions">
</span>
<span id="execution-results-POSTapi-comments--comment_id--reactions" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-comments--comment_id--reactions"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-comments--comment_id--reactions"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-comments--comment_id--reactions" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-comments--comment_id--reactions">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-comments--comment_id--reactions" data-method="POST"
      data-path="api/comments/{comment_id}/reactions"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-comments--comment_id--reactions', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-comments--comment_id--reactions"
                    onclick="tryItOut('POSTapi-comments--comment_id--reactions');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-comments--comment_id--reactions"
                    onclick="cancelTryOut('POSTapi-comments--comment_id--reactions');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-comments--comment_id--reactions"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/comments/{comment_id}/reactions</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-comments--comment_id--reactions"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-comments--comment_id--reactions"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>comment_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="comment_id"                data-endpoint="POSTapi-comments--comment_id--reactions"
               value="16"
               data-component="url">
    <br>
<p>The ID of the comment. Example: <code>16</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="POSTapi-comments--comment_id--reactions"
               value="support"
               data-component="body">
    <br>
<p>Example: <code>support</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>like</code></li> <li><code>support</code></li> <li><code>angry</code></li> <li><code>useful</code></li></ul>
        </div>
        </form>

                <h1 id="incidentes">Incidentes</h1>

    <p>APIs para gestionar los reportes ciudadanos de incidentes, incluyendo soporte a filtros y geolocalización.</p>

                                <h2 id="incidentes-POSTapi-incidents">Reportar nuevo incidente.</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Crea y registra un nuevo evento de seguridad en la base de datos de manera centralizada.
Recibe coordenadas geográficas (latitud, longitud), detalles en texto y recursos multimedia opcionales (fotos).
Tras registrar el incidente, el sistema analiza si el evento detona alguna alerta en la zona.</p>

<span id="example-requests-POSTapi-incidents">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://guardianapp.test/api/incidents" \
    --header "Authorization: Bearer {YOUR_AUTH_KEY}" \
    --header "Content-Type: multipart/form-data" \
    --header "Accept: application/json" \
    --form "category_id=2"\
    --form "description=Sospechoso intentando abrir vehículo estacionado."\
    --form "latitude=4.6097"\
    --form "longitude=-74.0817"\
    --form "location_description=Frente al parqueadero norte."\
    --form "privacy_level=ANONYMOUS"\
    --form "photos[]=@C:\Users\pcjul\AppData\Local\Temp\php3A8E.tmp" </code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/incidents"
);

const headers = {
    "Authorization": "Bearer {YOUR_AUTH_KEY}",
    "Content-Type": "multipart/form-data",
    "Accept": "application/json",
};

const body = new FormData();
body.append('category_id', '2');
body.append('description', 'Sospechoso intentando abrir vehículo estacionado.');
body.append('latitude', '4.6097');
body.append('longitude', '-74.0817');
body.append('location_description', 'Frente al parqueadero norte.');
body.append('privacy_level', 'ANONYMOUS');
body.append('photos[]', document.querySelector('input[name="photos[]"]').files[0]);

fetch(url, {
    method: "POST",
    headers,
    body,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-incidents">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 205,
    &quot;user_id&quot;: 8,
    &quot;category_id&quot;: 2,
    &quot;description&quot;: &quot;Sospechoso intentando abrir veh&iacute;culo estacionado.&quot;,
    &quot;location_description&quot;: &quot;Frente al parqueadero norte.&quot;,
    &quot;privacy_level&quot;: &quot;ANONYMOUS&quot;,
    &quot;status&quot;: &quot;reported&quot;,
    &quot;created_at&quot;: &quot;2026-04-05T21:00:00.000000Z&quot;,
    &quot;photos&quot;: []
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-incidents" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-incidents"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-incidents"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-incidents" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-incidents">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-incidents" data-method="POST"
      data-path="api/incidents"
      data-authed="1"
      data-hasfiles="1"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-incidents', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-incidents"
                    onclick="tryItOut('POSTapi-incidents');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-incidents"
                    onclick="cancelTryOut('POSTapi-incidents');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-incidents"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/incidents</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-incidents"
               value="Bearer {YOUR_AUTH_KEY}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {YOUR_AUTH_KEY}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-incidents"
               value="multipart/form-data"
               data-component="header">
    <br>
<p>Example: <code>multipart/form-data</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-incidents"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>category_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="category_id"                data-endpoint="POSTapi-incidents"
               value="2"
               data-component="body">
    <br>
<p>ID de la categoría de delito aplicable al evento. Example: <code>2</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="POSTapi-incidents"
               value="Sospechoso intentando abrir vehículo estacionado."
               data-component="body">
    <br>
<p>Descripción textual y detallada del evento presenciado. Example: <code>Sospechoso intentando abrir vehículo estacionado.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>latitude</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="latitude"                data-endpoint="POSTapi-incidents"
               value="4.6097"
               data-component="body">
    <br>
<p>Latitud de la ubicación del incidente según GPS. Example: <code>4.6097</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>longitude</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="longitude"                data-endpoint="POSTapi-incidents"
               value="-74.0817"
               data-component="body">
    <br>
<p>Longitud de la ubicación del incidente según GPS. Example: <code>-74.0817</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>location_description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="location_description"                data-endpoint="POSTapi-incidents"
               value="Frente al parqueadero norte."
               data-component="body">
    <br>
<p>Descripción adicional para facilitar la localización exacta. Example: <code>Frente al parqueadero norte.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>privacy_level</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="privacy_level"                data-endpoint="POSTapi-incidents"
               value="ANONYMOUS"
               data-component="body">
    <br>
<p>Nivel de privacidad requerido para proteger la identidad del ciudadano ('ANONYMOUS' o 'IDENTIFIED'). Example: <code>ANONYMOUS</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>photos</code></b>&nbsp;&nbsp;
<small>file[]</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="file" style="display: none"
                              name="photos[0]"                data-endpoint="POSTapi-incidents"
               data-component="body">
        <input type="file" style="display: none"
               name="photos[1]"                data-endpoint="POSTapi-incidents"
               data-component="body">
    <br>
<p>(Opcional) Subida de hasta 3 fotografías como evidencia visual del suceso (Máx. 10MB por archivo).</p>
        </div>
        </form>

                    <h2 id="incidentes-GETapi-incidents">Listado paginado de incidentes.</h2>

<p>
</p>

<p>Obtiene una lista de los incidentes reportados ordenados desde el más reciente. Este endpoint facilita la revisión de eventos e incluye información básica del reporte y su clasificación de seguridad.</p>

<span id="example-requests-GETapi-incidents">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://guardianapp.test/api/incidents?category_id=3&amp;status=reported&amp;days=30&amp;page=1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/incidents"
);

const params = {
    "category_id": "3",
    "status": "reported",
    "days": "30",
    "page": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-incidents">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;current_page&quot;: 1,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1500,
            &quot;category_id&quot;: 3,
            &quot;user_id&quot;: 200,
            &quot;description&quot;: &quot;Se presenci&oacute; una pelea...&quot;,
            &quot;status&quot;: &quot;reported&quot;,
            &quot;created_at&quot;: &quot;2026-04-02T10:00:00.000000Z&quot;,
            &quot;category&quot;: {
                &quot;id&quot;: 3,
                &quot;name&quot;: &quot;Disturbios&quot;,
                &quot;color&quot;: &quot;#f39c12&quot;,
                &quot;icon&quot;: &quot;warning&quot;
            },
            &quot;user&quot;: {
                &quot;id&quot;: 200,
                &quot;name&quot;: &quot;Usuario An&oacute;nimo&quot;
            }
        }
    ],
    &quot;from&quot;: 1,
    &quot;last_page&quot;: 10,
    &quot;per_page&quot;: 20,
    &quot;to&quot;: 20,
    &quot;total&quot;: 200
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-incidents" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-incidents"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-incidents"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-incidents" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-incidents">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-incidents" data-method="GET"
      data-path="api/incidents"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-incidents', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-incidents"
                    onclick="tryItOut('GETapi-incidents');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-incidents"
                    onclick="cancelTryOut('GETapi-incidents');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-incidents"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/incidents</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-incidents"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-incidents"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>category_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="category_id"                data-endpoint="GETapi-incidents"
               value="3"
               data-component="query">
    <br>
<p>Filtrar por ID de la categoría (tipo de delito). Example: <code>3</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-incidents"
               value="reported"
               data-component="query">
    <br>
<p>Estado actual del incidente: 'reported', 'investigating', 'resolved', 'dismissed'. Example: <code>reported</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>days</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="days"                data-endpoint="GETapi-incidents"
               value="30"
               data-component="query">
    <br>
<p>Limita los resultados a los últimos N días. Example: <code>30</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-incidents"
               value="1"
               data-component="query">
    <br>
<p>Número de página correspondiente de la paginación. Example: <code>1</code></p>
            </div>
                </form>

                    <h2 id="incidentes-GETapi-incidents--incident_id-">Detalles completos del incidente.</h2>

<p>
</p>

<p>Este endpoint despliega toda la información en profundidad de un único incidente, incluyendo estadísticas sociales asociadas. Principalmente se utiliza para las vistas de detalle de la aplicación ciudadana para comentar y reaccionar.</p>

<span id="example-requests-GETapi-incidents--incident_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://guardianapp.test/api/incidents/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://guardianapp.test/api/incidents/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-incidents--incident_id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 205,
    &quot;description&quot;: &quot;Sospechoso intentando abrir veh&iacute;culo estacionado.&quot;,
    &quot;status&quot;: &quot;reported&quot;,
    &quot;privacy_level&quot;: &quot;ANONYMOUS&quot;,
    &quot;social_stats&quot;: {
        &quot;comments_count&quot;: 5,
        &quot;reactions_count&quot;: 12,
        &quot;reaction_types&quot;: {
            &quot;like&quot;: 10,
            &quot;care&quot;: 2
        }
    },
    &quot;category&quot;: {
        &quot;id&quot;: 2,
        &quot;name&quot;: &quot;Vandalismo/Robo Vehicular&quot;
    },
    &quot;user&quot;: {
        &quot;id&quot;: 8,
        &quot;name&quot;: &quot;Carlos Gomez&quot;,
        &quot;profile_photo_path&quot;: &quot;profiles/photo.jpg&quot;
    },
    &quot;comments&quot;: [],
    &quot;photos&quot;: []
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-incidents--incident_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-incidents--incident_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-incidents--incident_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-incidents--incident_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-incidents--incident_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-incidents--incident_id-" data-method="GET"
      data-path="api/incidents/{incident_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-incidents--incident_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-incidents--incident_id-"
                    onclick="tryItOut('GETapi-incidents--incident_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-incidents--incident_id-"
                    onclick="cancelTryOut('GETapi-incidents--incident_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-incidents--incident_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/incidents/{incident_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-incidents--incident_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-incidents--incident_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>incident_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="incident_id"                data-endpoint="GETapi-incidents--incident_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the incident. Example: <code>1</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>incident</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="incident"                data-endpoint="GETapi-incidents--incident_id-"
               value="205"
               data-component="url">
    <br>
<p>ID numérico del incidente a consultar. Example: <code>205</code></p>
            </div>
                    </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
