import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart' as latlong;
import 'package:mobile_technicien/core/theme/app_theme.dart';

/// Mini-carte de localisation en lecture seule — flutter_map + OpenStreetMap,
/// cohérent avec l'intégration Leaflet/OSM déjà utilisée côté web (pas de
/// Google Maps ailleurs dans le projet). Réutilisée par la fiche chantier et
/// l'écran de rapport plutôt que dupliquée dans chacun.
class MiniLocationMap extends StatelessWidget {
  final double latitude;
  final double longitude;
  final double height;
  final double zoom;

  const MiniLocationMap({
    super.key,
    required this.latitude,
    required this.longitude,
    this.height = 180,
    this.zoom = 15,
  });

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(12),
      child: SizedBox(
        height: height,
        child: IgnorePointer(
          child: FlutterMap(
            options: MapOptions(
              initialCenter: latlong.LatLng(latitude, longitude),
              initialZoom: zoom,
              interactionOptions: const InteractionOptions(flags: InteractiveFlag.none),
            ),
            children: [
              TileLayer(
                urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                userAgentPackageName: 'com.technitrack.mobile_technicien',
              ),
              MarkerLayer(markers: [
                Marker(
                  point: latlong.LatLng(latitude, longitude),
                  width: 36,
                  height: 36,
                  child: const Icon(Icons.location_on, color: AppColors.brand, size: 36),
                ),
              ]),
            ],
          ),
        ),
      ),
    );
  }
}
