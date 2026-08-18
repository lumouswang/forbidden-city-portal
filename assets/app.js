// Центр: Запретный город (можешь поменять)
const CENTER = [116.397, 39.918];

// Загружаем данные с PHP
async function loadJSON(url) {
  const res = await fetch(url);
  if (!res.ok) throw new Error(`Ошибка загрузки: ${url}`);
  return res.json();
}

const map = new maplibregl.Map({
  container: 'map',
  style: {
    version: 8,
    sources: {
      osm: {
        type: 'raster',
        tiles: ['https://a.tile.openstreetmap.org/{z}/{x}/{y}.png'],
        tileSize: 256,
        attribution: '© OpenStreetMap contributors'
      }
    },
    layers: [{ id: 'osm', type: 'raster', source: 'osm' }]
  },
  center: CENTER,
  zoom: 16,
  pitch: 55,
  bearing: 0,
  antialias: true
});

map.addControl(new maplibregl.NavigationControl(), 'top-left');

Promise.all([
  loadJSON('data/objects.json'),
  loadJSON('data/geometry.geojson')
]).then(([objects, geojson]) => {
  const objectMap = new Map(objects.map(o => [Number(o.id), o]));

  // Пробрасываем title/description/height в geojson properties
  geojson.features = geojson.features.map(f => {
    const id = Number(f.properties.id);
    const obj = objectMap.get(id) || {};
    f.properties.title = obj.title || `Объект #${id}`;
    f.properties.description = obj.description || 'Описание будет добавлено позже.';
    f.properties.height = Number(obj.height || f.properties.height || 10);
    return f;
  });

  map.on('load', () => {
    map.addSource('museum', {
      type: 'geojson',
      data: geojson
    });

    // 3D здания (экструзия)
    map.addLayer({
      id: 'museum-3d',
      type: 'fill-extrusion',
      source: 'museum',
      paint: {
        'fill-extrusion-color': [
          'case',
          ['boolean', ['feature-state', 'hover'], false],
          '#ff7a00',
          '#d4aa70'
        ],
        'fill-extrusion-height': ['get', 'height'],
        'fill-extrusion-base': 0,
        'fill-extrusion-opacity': 0.95
      }
    });

    // Контуры
    map.addLayer({
      id: 'museum-outline',
      type: 'line',
      source: 'museum',
      paint: {
        'line-color': '#5b3a1a',
        'line-width': 1.5
      }
    });

    let hoveredId = null;

    map.on('mousemove', 'museum-3d', (e) => {
      map.getCanvas().style.cursor = 'pointer';
      if (!e.features || !e.features.length) return;

      const f = e.features[0];
      const fid = f.id ?? Number(f.properties.id);

      if (hoveredId !== null) {
        map.setFeatureState({ source: 'museum', id: hoveredId }, { hover: false });
      }
      hoveredId = fid;
      map.setFeatureState({ source: 'museum', id: hoveredId }, { hover: true });
    });

    map.on('mouseleave', 'museum-3d', () => {
      map.getCanvas().style.cursor = '';
      if (hoveredId !== null) {
        map.setFeatureState({ source: 'museum', id: hoveredId }, { hover: false });
      }
      hoveredId = null;
    });

    map.on('click', 'museum-3d', (e) => {
      if (!e.features || !e.features.length) return;
      const f = e.features[0].properties;

      const panel = document.getElementById('infoPanel');
      document.getElementById('objTitle').textContent = `${f.id}. ${f.title}`;
      document.getElementById('objDesc').textContent = f.description;
      document.getElementById('objMeta').textContent = `Высота: ${f.height} м`;
      panel.classList.remove('hidden');
    });

    document.getElementById('closePanel').addEventListener('click', () => {
      document.getElementById('infoPanel').classList.add('hidden');
    });

    // Подгоняем камеру под все объекты
    const bounds = new maplibregl.LngLatBounds();
    geojson.features.forEach(ft => {
      ft.geometry.coordinates[0].forEach(coord => bounds.extend(coord));
    });
    map.fitBounds(bounds, { padding: 40, duration: 0 });
  });
}).catch(err => {
  console.error(err);
  alert('Не удалось загрузить данные карты. Проверь файлы data/objects.json и data/geometry.geojson');
});