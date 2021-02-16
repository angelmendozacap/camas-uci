<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{hospital.name}}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg md:grid md:grid-cols-2 grid-col-4">
          <ul class="px-6 py-4 bg-white space-y-2">
            <li>
              Dirección: <span>{{hospital.address}}</span>
            </li>

            <li>
              Provincia: <span>{{hospital.province}}</span>
            </li>

            <li>
              Distrito: <span>{{hospital.district}}</span>
            </li>

            <li>
              Teléfono: <span>{{hospital.phone}}</span>
            </li>
          </ul>

          <div class="h-0 w-full relative overflow-hidden ratio-16/9">
            <div class="w-full h-full absolute top-0 left-0 object-cover rounded" ref="map-location"></div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Map from 'ol/Map';
import TileLayer from 'ol/layer/Tile';
import OSM from 'ol/source/OSM';
import { Feature, View } from 'ol';
import PointGeom from 'ol/geom/Point';
import VectorSource from 'ol/source/Vector';
import VectorLayer from 'ol/layer/Vector';
import Style from 'ol/style/Style';
import Icon from 'ol/style/Icon';
import { fromLonLat, transform } from 'ol/proj';

export default {
  components: {
    AppLayout,
  },
  props: {
    hospital: Object,
  },
  data() {
    return {
      map: null,
    }
  },
  mounted() {
    const feature = new Feature({
      geometry: new PointGeom(
        transform([parseFloat(this.hospital.latitude), parseFloat(this.hospital.longitude)], 'EPSG:4326', 'EPSG:3857')
      ),
    });

    feature.setStyle(new Style({
      image: new Icon({
        anchor: [0.5, 46],
        anchorXUnits: 'fraction',
        anchorYUnits: 'pixels',
        opacity: 0.75,
        src: 'https://raw.githubusercontent.com/do-community/travellist-laravel-demo/main/public/img/marker_visited.png',
      }),
    }));

    const vectorLayer = new VectorLayer({
      source: new VectorSource({
        features: [feature],
      }),
    });

    this.map = new Map({
      target: this.$refs['map-location'],
      layers: [
        new TileLayer({
          source: new OSM(),
        }),
        vectorLayer,
      ],
      view: new View({
        center: fromLonLat([this.hospital.latitude, this.hospital.longitude]),
        zoom: 15,
        constrainResolution: true,
      })
    })
  }
}
</script>
