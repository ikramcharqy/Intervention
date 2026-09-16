import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/features/home/domain/entities/mission.dart';
import 'package:mobile_technicien/features/missions/domain/entities/mission_status_filter.dart';
import 'package:mobile_technicien/features/missions/domain/usecases/get_missions_usecase.dart';

part 'missions_event.dart';
part 'missions_state.dart';

class MissionsBloc extends Bloc<MissionsEvent, MissionsState> {
  final GetMissionsUsecase getMissionsUsecase;

  MissionsBloc({required this.getMissionsUsecase}) : super(const MissionsLoading()) {
    on<MissionsRequested>(_onRequested);
    on<MissionsRefreshed>(_onRefreshed);
    on<MissionsFilterChanged>(_onFilterChanged);
    on<MissionsSearchChanged>(_onSearchChanged);
  }

  Future<void> _onRequested(MissionsRequested event, Emitter<MissionsState> emit) async {
    emit(const MissionsLoading());
    await _load(emit, filter: MissionStatusFilter.toutes, searchQuery: '');
  }

  Future<void> _onRefreshed(MissionsRefreshed event, Emitter<MissionsState> emit) async {
    // Garde le filtre/la recherche en cours pendant le rafraîchissement,
    // cohérent avec HomeBloc._onRefreshed (pas de retour au skeleton).
    final current = state;
    final filter = switch (current) {
      MissionsLoaded(filter: final f) => f,
      MissionsEmpty(filter: final f) => f,
      _ => MissionStatusFilter.toutes,
    };
    final query = switch (current) {
      MissionsLoaded(searchQuery: final q) => q,
      MissionsEmpty(searchQuery: final q) => q,
      _ => '',
    };
    await _load(emit, filter: filter, searchQuery: query);
  }

  /// Étape 2.2 : changement de filtre = simple recalcul sur le cache déjà en
  /// mémoire, aucun appel réseau supplémentaire.
  void _onFilterChanged(MissionsFilterChanged event, Emitter<MissionsState> emit) {
    final current = state;
    final allMissions = switch (current) {
      MissionsLoaded(allMissions: final m) => m,
      MissionsEmpty(allMissions: final m) => m,
      _ => null,
    };
    if (allMissions == null) return; // pas encore chargé — rien à refiltrer

    final query = switch (current) {
      MissionsLoaded(searchQuery: final q) => q,
      MissionsEmpty(searchQuery: final q) => q,
      _ => '',
    };
    emit(_buildState(allMissions, event.filter, query));
  }

  void _onSearchChanged(MissionsSearchChanged event, Emitter<MissionsState> emit) {
    final current = state;
    final allMissions = switch (current) {
      MissionsLoaded(allMissions: final m) => m,
      MissionsEmpty(allMissions: final m) => m,
      _ => null,
    };
    if (allMissions == null) return;

    final filter = switch (current) {
      MissionsLoaded(filter: final f) => f,
      MissionsEmpty(filter: final f) => f,
      _ => MissionStatusFilter.toutes,
    };
    emit(_buildState(allMissions, filter, event.query));
  }

  Future<void> _load(
    Emitter<MissionsState> emit, {
    required MissionStatusFilter filter,
    required String searchQuery,
  }) async {
    final result = await getMissionsUsecase();
    result.when(
      success: (missions) => emit(_buildState(missions, filter, searchQuery)),
      failure: (f) => emit(MissionsError(f.message)),
    );
  }

  /// Tri par date prévue croissante (la plus proche en premier), missions
  /// sans date reléguées en fin de liste plutôt que de casser le tri
  /// (Étape 3.6).
  MissionsState _buildState(List<Mission> allMissions, MissionStatusFilter filter, String searchQuery) {
    final normalizedQuery = searchQuery.trim().toLowerCase();

    final filtered = allMissions.where((m) {
      if (!filter.matches(m)) return false;
      if (normalizedQuery.isEmpty) return true;
      return m.chantierNom.toLowerCase().contains(normalizedQuery) ||
          m.codeIntervention.toLowerCase().contains(normalizedQuery);
    }).toList()
      ..sort((a, b) {
        final da = a.datePrevueDebut;
        final db = b.datePrevueDebut;
        if (da == null && db == null) return 0;
        if (da == null) return 1;
        if (db == null) return -1;
        return da.compareTo(db);
      });

    if (filtered.isEmpty) {
      return MissionsEmpty(allMissions: allMissions, filter: filter, searchQuery: searchQuery);
    }
    return MissionsLoaded(
      allMissions: allMissions,
      filteredMissions: filtered,
      filter: filter,
      searchQuery: searchQuery,
    );
  }
}
