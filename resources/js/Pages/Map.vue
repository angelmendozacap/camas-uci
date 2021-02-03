<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mapa</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <section class="p-4 bg-white border-b border-gray-200">
            <div class="h-0 w-full relative overflow-hidden ratio-16/9">
              <div class="w-full h-full absolute top-0 left-0 object-cover rounded" ref="map-root"></div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import Map from 'ol/Map';
import View from 'ol/View';
import Feature from 'ol/Feature';
import OSM from 'ol/source/OSM';
import VectorSource from 'ol/source/Vector';
import PointGeom from 'ol/geom/Point';
import { fromLonLat, transform } from 'ol/proj';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';

import AppLayout from "@/Layouts/AppLayout";
import Style from 'ol/style/Style';
import Icon from 'ol/style/Icon';

export default {
  components: {
    AppLayout,
  },
  props: {
    hospitals: Array,
  },
  data() {
    return {
      initialLon: parseFloat(-11.641952),
      initialLat: parseFloat(-74.945649),
      map: null,
      iconURL: 'https://raw.githubusercontent.com/do-community/travellist-laravel-demo/main/public/img/marker_togo.png',
    }
  },
  methods: {
    addMapPoint() {
      const featuresArr = this.hospitals.map(hospital => {
        const feature = new Feature({
          geometry: new PointGeom(
            transform([parseFloat(hospital.latitude), parseFloat(hospital.longitude)], 'EPSG:4326', 'EPSG:3857')
          ),
        })

        feature.setProperties({ ...hospital })
        feature.setId(hospital.id)

        return feature
      })

      const vectorLayer = new VectorLayer({
        source: new VectorSource({
          features: [...featuresArr],
        }),
        style: new Style({
          image: new Icon({
            anchor: [0.5, 46],
            anchorXUnits: 'fraction',
            anchorYUnits: 'pixels',
            opacity: 0.75,
            src: this.iconURL,
          }),
        })
      })

      this.map.addLayer(vectorLayer)
    }
  },
  mounted() {
    this.map = new Map({
      target: this.$refs['map-root'],
      layers: [
        new TileLayer({
          source: new OSM(),
        })
      ],
      view: new View({
        center: fromLonLat([this.initialLat, this.initialLon]),
        zoom: 8,
        constrainResolution: true,
      })
    })

    this.addMapPoint()
  }
};
</script>
