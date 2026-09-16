import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/network/dio_error_mapper.dart';
import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/home/data/datasources/home_remote_datasource.dart';
import 'package:mobile_technicien/features/home/domain/entities/home_summary.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/home/domain/repositories/home_repository.dart';

class HomeRepositoryImpl implements HomeRepository {
  final HomeRemoteDatasource remote;
  HomeRepositoryImpl(this.remote);

  @override
  Future<Result<HomeSummary>> getHomeSummary() async {
    try {
      final missions = await remote.getMissions();
      final unreadCount = await remote.getUnreadNotificationsCount();

      final actives = missions.where((m) => !m.estCloturee).toList()
        ..sort((a, b) {
          final da = a.datePrevueDebut;
          final db = b.datePrevueDebut;
          if (da == null && db == null) return 0;
          if (da == null) return 1;
          if (db == null) return -1;
          return da.compareTo(db);
        });

      final Mission? prochaine = actives.isNotEmpty ? actives.first : null;

      final aRealiser = missions
          .where((m) => m.statut == 'Planifiee' || m.statut == 'Affectee' || m.statut == 'Acceptee')
          .length;
      final enCours = missions.where((m) => m.statut == 'En cours' || m.statut == 'Suspendue').length;
      final terminees = missions.where((m) => m.statut == 'Terminee' || m.statut == 'Validee').length;

      // Étape 4 "Missions récentes" : les clôturées les plus récentes en
      // premier ; si moins de 3, on complète avec les "En cours" (hors la
      // mission déjà mise en avant comme "Prochaine mission") plutôt que de
      // laisser la section vide.
      final closturees = missions.where((m) => m.estCloturee).toList()
        ..sort((a, b) => (b.datePrevueDebut ?? DateTime(0)).compareTo(a.datePrevueDebut ?? DateTime(0)));
      final enCoursHorsProchaine = missions
          .where((m) => m.statut == 'En cours' && m.id != prochaine?.id)
          .toList();
      final missionsRecentes = [...closturees, ...enCoursHorsProchaine].take(3).toList();

      return Success(HomeSummary(
        prochaineMission: prochaine,
        aRealiser: aRealiser,
        enCours: enCours,
        terminees: terminees,
        notificationsNonLues: unreadCount,
        missionsRecentes: missionsRecentes,
      ));
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> startMission(int missionId) async {
    try {
      await remote.startIntervention(missionId);
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }
}
