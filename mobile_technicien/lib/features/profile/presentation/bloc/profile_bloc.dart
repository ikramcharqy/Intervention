import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/features/profile/domain/entities/profile_data.dart';
import 'package:mobile_technicien/features/profile/domain/usecases/profile_usecases.dart';

part 'profile_event.dart';
part 'profile_state.dart';

class ProfileBloc extends Bloc<ProfileEvent, ProfileState> {
  final GetProfileUsecase getProfileUsecase;

  ProfileBloc({required this.getProfileUsecase}) : super(const ProfileLoading()) {
    on<ProfileRequested>(_onRequested);
  }

  Future<void> _onRequested(ProfileRequested event, Emitter<ProfileState> emit) async {
    emit(const ProfileLoading());
    final result = await getProfileUsecase();
    result.when(
      success: (data) => emit(ProfileLoaded(data)),
      failure: (f) => emit(ProfileError(f.message)),
    );
  }
}
