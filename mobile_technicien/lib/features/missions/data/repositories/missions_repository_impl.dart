import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/network/dio_error_mapper.dart';
import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/home/data/datasources/home_remote_datasource.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/missions/domain/repositories/missions_repository.dart';

/// Réutilise HomeRemoteDatasource.getMissions() (GET /api/interventions) —
/// c'est le même appel déjà utilisé pour les compteurs de l'accueil, pas un
/// second endpoint : garantit que les deux écrans ne peuvent pas diverger.
class MissionsRepositoryImpl implements MissionsRepository {
  final HomeRemoteDatasource remote;
  MissionsRepositoryImpl(this.remote);

  @override
  Future<Result<List<Mission>>> getMissions() async {
    try {
      final missions = await remote.getMissions();
      return Success(missions);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<Mission>> getMissionDetail(int missionId) async {
    try {
      final mission = await remote.getMissionDetail(missionId);
      return Success(mission);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> acceptMission(int missionId) async {
    try {
      await remote.acceptIntervention(missionId);
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> refuseMission(int missionId, String motif) async {
    try {
      await remote.refuseIntervention(missionId, motif);
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> startMission(int missionId, {double? latitude, double? longitude}) async {
    try {
      await remote.startIntervention(missionId, latitude: latitude, longitude: longitude);
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }
}
