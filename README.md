## Mapa de disponibilidad de camas UCI en la región Junín

![alt text](./readme-files/app.gif)

Aplicación que permite ver los hospitales en la regi´´on Junín y la disponibilidad de camas UCI.
1. Datos convertidos de archivos shapefile a SQL (del portal [GeoGpsPeru](https://www.geogpsperu.com/2017/09/establecimientos-de-salud-eess.html)) de los hospitales.
2. Reune datos actualizados diariamente de las camas UCI del portal web [OpenCovid](https://opencovid-peru.com/).
3. Conversión de datos CSV de las cantidad de camas UCI a JSON para su mejor lectura.
4. Como framework de desarrollo se uso Laravel y Vue para el cliente.
5. Para los mapas se uso la librería [OpenLayers](https://openlayers.org/), el cual se uso Javascript para renderizar los íconos gracias a la información en base de datos de los hospitales.
6. Para los gráficos se uso [ChartJS](https://www.chartjs.org/) el cual renderiza los datos de la cantidad de camas UCI de [OpenCovid](https://opencovid-peru.com/).
