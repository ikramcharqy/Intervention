import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/features/home/domain/entities/home_summary.dart';
import 'package:mobile_technicien/features/home/domain/usecases/get_home_summary_usecase.dart';

part 'home_event.dart';
part 'home_state.dart';

class HomeBloc extends Bloc<HomeEvent, HomeState> {
  final GetHomeSummaryUsecase getHomeSummaryUsecase;

  HomeBloc({required this.getHomeSummaryUsecase}) : super(const HomeLoading()) {
    on<HomeRequested>(_onRequested);
    on<HomeRefreshed>(_onRefreshed);
  }

  Future<void> _onRequested(HomeRequested event, Emitter<HomeState> emit) async {
    emit(const HomeLoading());
    await _load(emit);
  }

  Future<void> _onRefreshed(HomeRefreshed event, Emitter<HomeState> emit) async {
    // Étape 2.5 : tirer-pour-rafraîchir garde le contenu précédent affiché
    // pendant le rechargement plutôt que de repasser par le skeleton loader.
    await _load(emit);
  }

  Future<void> _load(Emitter<HomeState> emit) async {
    final result = await getHomeSummaryUsecase();
    result.when(
      success: (summary) => emit(HomeLoaded(summary)),
      failure: (f) => emit(HomeError(f.message)),
    );
  }
}
